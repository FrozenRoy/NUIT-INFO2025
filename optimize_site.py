import os
import shutil
import re
import json
import sys
from datetime import datetime

# Configuration
PROJECT_ROOT = os.path.dirname(os.path.abspath(__file__))
REPORT_FILE = os.path.join(PROJECT_ROOT, 'optimization_report.json')
BACKUP_DIR = os.path.join(PROJECT_ROOT, 'backup_' + datetime.now().strftime('%Y%m%d_%H%M%S'))
UNUSED_DIR = os.path.join(PROJECT_ROOT, 'unused_resources')

# Extensions to process
IMG_EXTS = {'.jpg', '.jpeg', '.png'}
CODE_EXTS = {'.css', '.js', '.html', '.php'}

def get_size(path):
    return os.path.getsize(path)

def format_size(size):
    for unit in ['B', 'KB', 'MB']:
        if size < 1024:
            return f"{size:.2f} {unit}"
        size /= 1024
    return f"{size:.2f} GB"

# --- Minification Logic ---

def minify_css(content):
    # Remove comments
    content = re.sub(r'/\*[\s\S]*?\*/', '', content)
    # Remove whitespace
    content = re.sub(r'\s+', ' ', content)
    content = re.sub(r'\s*([:;{}])\s*', r'\1', content)
    content = content.replace(';}', '}')
    return content.strip()

def minify_js(content):
    # Very basic JS minifier (removes comments and extra whitespace)
    # Note: A proper parser is recommended for production, but this works for simple scripts
    content = re.sub(r'//.*', '', content) # Single line comments
    content = re.sub(r'/\*[\s\S]*?\*/', '', content) # Multi line comments
    content = re.sub(r'\s+', ' ', content)
    content = re.sub(r'\s*([=+\-*/{}();,])\s*', r'\1', content)
    return content.strip()

def minify_html(content):
    # Remove comments
    content = re.sub(r'<!--(?!\[if).*?-->', '', content, flags=re.DOTALL)
    # Collapse whitespace
    content = re.sub(r'\s+', ' ', content)
    content = re.sub(r'>\s+<', '><', content)
    return content.strip()

# --- Image Optimization ---

def optimize_image(path):
    try:
        from PIL import Image
    except ImportError:
        return None, "PIL library not installed"

    try:
        img = Image.open(path)
        
        # Generate WebP path
        webp_path = os.path.splitext(path)[0] + '.webp'
        
        # Save as WebP
        img.save(webp_path, 'WEBP', quality=80, method=6)
        
        old_size = get_size(path)
        new_size = get_size(webp_path)
        
        if new_size < old_size:
            return webp_path, None
        else:
            # If WebP is bigger (rare but possible), keep original
            os.remove(webp_path)
            return None, "WebP was larger, kept original"
            
    except Exception as e:
        return None, str(e)

# --- Unused Resource Detection ---

def find_unused_resources(root_dir):
    # 1. Index all files
    all_files = []
    code_files = []
    
    exclude_dirs = {'.git', 'vendor', 'node_modules', '__pycache__', 'backup_', 'unused_resources'}
    
    for root, dirs, files in os.walk(root_dir):
        dirs[:] = [d for d in dirs if d not in exclude_dirs and not d.startswith('backup_')]
        for file in files:
            path = os.path.join(root, file)
            rel_path = os.path.relpath(path, root_dir).replace('\\', '/')
            ext = os.path.splitext(file)[1].lower()
            
            all_files.append({'path': path, 'rel': rel_path, 'ext': ext})
            if ext in CODE_EXTS:
                code_files.append(path)

    # 2. Build content index
    content_blob = ""
    for path in code_files:
        try:
            with open(path, 'r', encoding='utf-8', errors='ignore') as f:
                content_blob += f.read() + " "
        except:
            pass

    # 3. Check for references
    unused = []
    # Always keep these
    whitelist = {'index.php', 'style.css', 'script.js', 'favicon.ico', 'manifest.json', 'optimize_site.py'}
    
    for file in all_files:
        name = os.path.basename(file['path'])
        if name in whitelist:
            continue
            
        # Simple heuristic: check if filename appears in content blob
        # (False positives possible, but safe for "candidates")
        if name not in content_blob and file['ext'] not in {'.php', '.html'}: 
            # We assume PHP/HTML files are entry points or includes that might be dynamic
            # So we mostly look for unused images, css, js that are NOT referenced
            unused.append(file)

    return unused

# --- Main Execution ---

def main():
    print("Starting Optimization Module...")
    
    report = {
        'timestamp': str(datetime.now()),
        'actions': [],
        'savings': 0
    }

    # 1. Backup
    print(f"Creating backup in {BACKUP_DIR}...")
    shutil.copytree(PROJECT_ROOT, BACKUP_DIR, ignore=shutil.ignore_patterns('backup_*', 'unused_resources', '.git', 'vendor'))

    # 2. Optimize Images
    print("Optimizing images...")
    for root, dirs, files in os.walk(PROJECT_ROOT):
        if 'backup_' in root or 'unused_resources' in root or 'vendor' in root:
            continue
            
        for file in files:
            if os.path.splitext(file)[1].lower() in IMG_EXTS:
                path = os.path.join(root, file)
                new_path, error = optimize_image(path)
                
                if new_path:
                    saved = get_size(path) - get_size(new_path)
                    report['actions'].append({
                        'type': 'image_compression',
                        'file': file,
                        'saved_bytes': saved
                    })
                    report['savings'] += saved
                    print(f"Converted {file} to WebP (Saved {format_size(saved)})")
                elif error == "PIL library not installed":
                    print("Skipping images: PIL not installed.")
                    break

    # 3. Minify Code
    print("Minifying code...")
    for root, dirs, files in os.walk(PROJECT_ROOT):
        if 'backup_' in root or 'unused_resources' in root or 'vendor' in root:
            continue
            
        for file in files:
            ext = os.path.splitext(file)[1].lower()
            if ext in {'.css', '.js'}: # HTML/PHP minification can be risky with PHP tags, skipping for safety
                path = os.path.join(root, file)
                try:
                    with open(path, 'r', encoding='utf-8') as f:
                        content = f.read()
                    
                    original_size = len(content)
                    
                    if ext == '.css':
                        minified = minify_css(content)
                    elif ext == '.js':
                        minified = minify_js(content)
                    
                    if len(minified) < original_size:
                        with open(path, 'w', encoding='utf-8') as f:
                            f.write(minified)
                            
                        saved = original_size - len(minified)
                        report['actions'].append({
                            'type': 'minification',
                            'file': file,
                            'saved_bytes': saved
                        })
                        report['savings'] += saved
                        print(f"Minified {file} (Saved {format_size(saved)})")
                except Exception as e:
                    print(f"Error minifying {file}: {e}")

    # 4. Handle Unused Resources
    print("Analyzing unused resources...")
    unused_files = find_unused_resources(PROJECT_ROOT)
    if unused_files:
        if not os.path.exists(UNUSED_DIR):
            os.makedirs(UNUSED_DIR)
            
        for file in unused_files:
            try:
                dest = os.path.join(UNUSED_DIR, os.path.basename(file['path']))
                shutil.move(file['path'], dest)
                
                size = get_size(dest)
                report['actions'].append({
                    'type': 'unused_removal',
                    'file': file['rel'],
                    'saved_bytes': size
                })
                report['savings'] += size
                print(f"Moved unused file {file['rel']} to {UNUSED_DIR}")
            except Exception as e:
                print(f"Error moving {file['rel']}: {e}")

    # 5. Generate Report
    print("-" * 30)
    print(f"Optimization Complete.")
    print(f"Total Space Saved: {format_size(report['savings'])}")
    
    with open(REPORT_FILE, 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=4)
    print(f"Report saved to {REPORT_FILE}")

if __name__ == "__main__":
    main()
