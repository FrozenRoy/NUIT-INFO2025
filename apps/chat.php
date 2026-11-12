<div class="window draggable-window" id="app-chatbox" style="display:none; top: 100px; left: 400px; width: 350px; height: 500px;">
    <div class="window-title-bar">
        <span class="title"><i class="fas fa-comments"></i> Aristo NIRD</span>
        <div class="window-controls">
            <span class="close-btn" onclick="closeApp('app-chatbox')">✕</span>
        </div>
    </div>
    <div class="window-content chat-container">
        <div class="chat-messages" id="chat-messages">
            <div class="message system">
                <div class="message-content">Bienvenue chez Aristote le Distrait ! Posez vos questions.</br>Ceci est un ChatBOT gratuit, il pourrait subir quelques bugs.</div>
            </div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="chat-input" placeholder="Écrivez votre message..." autocomplete="off">
            <button id="chat-send-btn" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>
