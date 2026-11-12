import os
import json

# Configuration
PROJECT_ROOT = r'c:\wamp64\www\nuit_info'
OUTPUT_FILE = os.path.join(PROJECT_ROOT, 'eco_stats.json')

# Folders to exclude from the scan to avoid noise (libraries, git, etc.)
EXCLUDED_DIRS = {'.git', 'vendor', 'node_modules', '.vscode', '__pycache__'}
# Files to exclude from the scan
EXCLUDED_FILES = {'eco_stats.json', 'analyze_eco.py', '.env'}

def get_file_size(path):
    """Safely returns the size of a file in bytes."""
    try:
        return os.path.getsize(path)
    except OSError:
        return 0

def analyze_project(root_dir):
    """
    Scans the project directory to calculate size, file counts, and EcoIndex score.
    
    Args:
        root_dir (str): The root path of the project.
        
    Returns:
        dict: A dictionary containing all calculated statistics.
    """
    stats = {
        'total_size': 0,
        'file_count': 0,
        'images': [],
        'css': [],
        'js': [],
        'php': [],
        'other': [],
        'unused_candidates': []
    }
    
    all_files_rel_paths = []
    
    # 1. File System Scan
    # ---------------------------------------------------------
    for root, dirs, files in os.walk(root_dir):
        # Modify dirs in-place to skip excluded directories
        dirs[:] = [d for d in dirs if d not in EXCLUDED_DIRS]
            
        for file in files:
            if file in EXCLUDED_FILES:
                continue
                
            path = os.path.join(root, file)
            # Get relative path for cleaner output (e.g., "css/style.css")
            rel_path = os.path.relpath(path, root_dir).replace('\\', '/')
            size = get_file_size(path)
            ext = os.path.splitext(file)[1].lower()
            
            file_info = {'path': rel_path, 'size': size, 'name': file}
            
            # Update global stats
            stats['total_size'] += size
            stats['file_count'] += 1
            all_files_rel_paths.append(rel_path)

            # Categorize files
            if ext in {'.jpg', '.jpeg', '.png', '.gif', '.svg', '.webp', '.ico'}:
                stats['images'].append(file_info)
            elif ext == '.css':
                stats['css'].append(file_info)
            elif ext == '.js':
                stats['js'].append(file_info)
            elif ext == '.php':
                stats['php'].append(file_info)
            else:
                stats['other'].append(file_info)

    # 2. Dead Code Detection (Optimized)
    # ---------------------------------------------------------
    # Instead of opening every file for every candidate (O(N*M)), 
    # we read all source code once into a large set of tokens/strings.
    
    source_content_blob = ""
    source_files = stats['php'] + stats['js'] + stats['css'] + stats['other'] # HTML might be in 'other'
    
    for file_info in source_files:
        try:
            full_path = os.path.join(root_dir, file_info['path'])
            with open(full_path, 'r', encoding='utf-8', errors='ignore') as f:
                source_content_blob += f.read() + " " 
        except Exception:
            pass # Skip unreadable files

    # Check for references
    referenced_files = set()
    referenced_files.add('index.php') # Entry point is always used

    for rel_path in all_files_rel_paths:
        filename = os.path.basename(rel_path)
        
        # Heuristic: If the filename appears in the source blob, it's likely used.
        # Note: This is a simple string match, not a full AST parser.
        if filename in source_content_blob:
            referenced_files.add(rel_path)
        
        # Whitelist specific dynamic folders
        if rel_path.startswith('documents/') or rel_path.startswith('api/'):
            referenced_files.add(rel_path)

    # Identify unused files
    for rel_path in all_files_rel_paths:
        if rel_path not in referenced_files:
            stats['unused_candidates'].append(rel_path)

    # 3. EcoIndex Calculation (Simulation)
    # ---------------------------------------------------------
    # Formula based on EcoIndex methodology (simplified):
    # Impact depends heavily on DOM elements (not calculated here) and Data Transfer.
    
    # Calculate "Frontend Weight" (CSS + JS + Images + Main PHP)
    # We divide by 1024 to get Kilobytes (KB)
    page_weight_kb = (
        sum(f['size'] for f in stats['css']) + 
        sum(f['size'] for f in stats['js']) + 
        sum(f['size'] for f in stats['images']) +
        (os.path.getsize(os.path.join(root_dir, 'index.php')) if os.path.exists(os.path.join(root_dir, 'index.php')) else 0)
    ) / 1024
    
    # Estimate HTTP requests (1 HTML + CSS files + JS files + approx 5 images/fonts)
    request_count = 1 + len(stats['css']) + len(stats['js']) + 5 
    
    # Scoring Algorithm (0 to 100)
    # Heuristic: 
    # - Start at 100
    # - Lose 1 point per 10KB
    # - Lose 2 points per request
    score = 100 - (page_weight_kb / 10) - (request_count * 2)
    
    # Clamp score between 0 and 100
    score = max(0, min(100, score))
    
    # Assign Grade (A to E)
    if score >= 80: grade = 'A'
    elif score >= 60: grade = 'B'
    elif score >= 40: grade = 'C'
    elif score >= 20: grade = 'D'
    else: grade = 'E'
    
    stats['eco_score'] = round(score, 1)
    stats['eco_grade'] = grade
    stats['page_weight_kb'] = round(page_weight_kb, 2)
    stats['request_count_est'] = request_count
    
    return stats

if __name__ == "__main__":
    print(f"Starting analysis of: {PROJECT_ROOT}")
    try:
        data = analyze_project(PROJECT_ROOT)
        
        with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=4)
            
        print(f"Analysis complete.")
        print(f"Score: {data['eco_score']}/100 (Grade {data['eco_grade']})")
        print(f"Weight: {data['page_weight_kb']} KB")
        print(f"Report saved to: {OUTPUT_FILE}")
        
    except Exception as e:
        print(f"Error during analysis: {e}")
