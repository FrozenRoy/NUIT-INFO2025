<div class="window draggable-window" id="app-snake" style="display:none; top: 150px; left: 200px; width: 500px; height: 650px;">
    <div class="window-title-bar">
        <span class="title"><i class="fas fa-gamepad"></i> Snake Game</span>
        <div class="window-controls"><span class="close-btn" onclick="closeApp('app-snake')">✕</span></div>
    </div>
    <div class="window-content" style="display: flex; flex-direction: column; padding: 15px; background: #1a1a1a; gap: 10px;">
        
        <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 5px; border-left: 3px solid #00ff00;">
            <div style="font-size: 0.9em; color: #aaa; margin-bottom: 5px;">SCORE</div>
            <div style="font-size: 1.8em; color: #00ff00; font-weight: bold;" id="snake-score">0</div>
        </div>
        
        <div style="flex: 1; display: flex; justify-content: center; align-items: center; background: rgba(0,0,0,0.5); border-radius: 5px; overflow: hidden;">
            <canvas id="snake-canvas" style="border: 2px solid #00ff00; display: block;"></canvas>
        </div>
        
        <div id="snake-game-over" style="display: none; background: #ff3333; color: white; padding: 12px; border-radius: 5px; text-align: center; font-weight: bold; margin-bottom: 5px;"></div>
        
        <div style="display: flex; gap: 10px;">
            <button id="snake-start-btn" onclick="snakeGame.startGame()" style="flex: 1; padding: 12px; background: #00ff00; color: #000; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 1em; transition: background 0.2s;">
                Commencer
            </button>
            <button onclick="snakeGame.resetGame()" style="flex: 1; padding: 12px; background: #3333ff; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 1em; transition: background 0.2s;">
                Réinitialiser
            </button>
        </div>
        
        <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 5px; font-size: 0.85em; color: #aaa; line-height: 1.6;">
            <strong style="color: #00ff00;">Contrôles:</strong><br>
            ⬆️ ⬇️ ⬅️ ➡️ ou W A S D<br>
            ESPACE pour recommencer
        </div>
    </div>
</div>