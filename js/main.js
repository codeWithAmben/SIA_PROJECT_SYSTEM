// Main site scripts (moved from inline HTML)
// --- 0. AI CONFIGURATION ---
const apiKey = ""; // Add your API key here or load from secure place
const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`;

// Ensure we attach events after DOM is ready when using defer or script at the end
(function () {
  'use strict';

  // --- 1. AI Features Logic ---
  function toggleAiChat() {
    const chat = document.getElementById('aiChatModal');
    if (!chat) return;
    chat.classList.toggle('hidden');
  }

  async function askAssistant() {
    const inputEl = document.getElementById('aiInput');
    const historyEl = document.getElementById('chatHistory');
    const sendBtn = document.getElementById('aiSendBtn');
    if (!inputEl || !historyEl || !sendBtn) return;
    const userText = inputEl.value.trim();

    if (!userText) return;

    // UI Updates
    inputEl.value = '';
    sendBtn.disabled = true;

    // Append User Message
    historyEl.innerHTML += `\n                <div class="bg-green-100 p-3 rounded-bl-lg rounded-tl-lg rounded-tr-lg self-end max-w-[90%] border border-green-200 text-gray-800">\n                    ${userText}\n                </div>`;
    historyEl.scrollTop = historyEl.scrollHeight;

    // Loading Indicator
    const loadingId = 'loading-' + Date.now();
    historyEl.innerHTML += `\n                <div id="${loadingId}" class="self-start text-gray-400 text-xs italic ml-2">\n                    <i class="fa-solid fa-circle-notch fa-spin"></i> Thinking...\n                </div>`;

    try {
      // Call Gemini API
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          contents: [{ parts: [{ text: "You are an expert agricultural advisor. Answer helpful and concisely. User asks: " + userText }] }]
        })
      });

      const data = await response.json();
      const aiText = data.candidates?.[0]?.content?.parts?.[0]?.text || "Sorry, I couldn't process that.";

      // Remove loading
      const node = document.getElementById(loadingId);
      if (node) node.remove();

      // Append AI Message (Parsed Markdown)
      historyEl.innerHTML += `\n                    <div class="bg-blue-50 p-3 rounded-br-lg rounded-bl-lg rounded-tr-lg self-start max-w-[90%] border border-blue-100 text-gray-700">\n                        ${marked.parse(aiText)}\n                    </div>`;

    } catch (error) {
      console.error(error);
      const node = document.getElementById(loadingId);
      if (node) node.remove();
      historyEl.innerHTML += `<div class="text-red-500 text-xs self-start">Connection Error. Retrying...</div>`;
    }

    historyEl.scrollTop = historyEl.scrollHeight;
    sendBtn.disabled = false;
  }

  // Allow 'Enter' key to send
  function initAiInput() {
    const aiInput = document.getElementById('aiInput');
    if (!aiInput) return;
    aiInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') askAssistant();
    });
  }

  // Feature B: Newsletter Auto-Drafter
  async function generateAiNewsletter() {
    const btn = document.getElementById('btnGenerateNews');
    if (!btn) return;
    const originalBtnText = btn.innerHTML;
    const container = document.getElementById('aiDraftContainer');
    const contentBox = document.getElementById('aiDraftContent');

    // 1. Scrape Notes from DOM
    const noteElements = document.querySelectorAll('#notesContainer h3, #notesContainer p');
    let notesText = "";
    noteElements.forEach(el => notesText += el.innerText + " ");

    if(notesText.length < 20) {
      alert("Please add some notes first so the AI has something to write about!");
      return;
    }

    // 2. UI Loading State
    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Drafting...`;

    // 3. Construct Prompt
    const prompt = `\n                Act as a friendly farm manager. \n                Read these raw notes from the field: "${notesText}". \n                Write a short, engaging 1-paragraph newsletter update for our customers summarizing this week's progress. \n                Use emojis. Keep it under 50 words.\n            `;

    try {
      // 4. API Call
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ contents: [{ parts: [{ text: prompt }] }] })
      });

      const data = await response.json();
      const draft = data.candidates?.[0]?.content?.parts?.[0]?.text || "AI generation failed.";

      // 5. Display Result
      if (container) container.classList.remove('hidden');
      if (contentBox) contentBox.innerHTML = marked.parse(draft);

      // Auto-scroll to draft
      if (container) container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    } catch (error) {
      alert("AI Service Unavailable. Please try again.");
    }

    btn.disabled = false;
    btn.innerHTML = originalBtnText;
  }

  // --- 2. Standard Logic (Map, Notes, Forms) ---
  function handleMapClick(event, type) {
    if (event) event.preventDefault();
    const modal = document.getElementById('dataModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');
    const icon = document.getElementById('modalIcon');
    if (!modal || !title || !content || !icon) return;

    modal.classList.remove('hidden');
    content.innerHTML = '<div class="flex justify-center p-4"><i class="fa-solid fa-circle-notch fa-spin text-green-600"></i></div>';

    const config = {
      'animals': { title: 'Livestock Data (animals.xml)', icon: 'fa-cow' },
      'crops': { title: 'Crop Yields (crops.xml)', icon: 'fa-wheat-awn' },
      'users': { title: 'Farm Staff (users.xml)', icon: 'fa-users' },
      'tasks': { title: 'Pending Tasks (tasks.xml)', icon: 'fa-list-check' }
    };

    title.innerText = config[type] ? config[type].title : 'Data';
    icon.className = `fa-solid ${config[type] ? config[type].icon : 'fa-file'} text-green-600`;

    fetch(`php/load-data.php?file=${type}`)
      .then(res => res.json())
      .then(data => renderData(data, content))
      .catch(err => {
        content.innerHTML = '<p class="text-red-500 text-center">Failed to load XML data.</p>';
      });
  }

  function renderData(data, container) {
    if (!container) return;
    let html = '';
    let items = data;
    const keys = Object.keys(data || {});
    if (keys.length === 1 && (Array.isArray(data[keys[0]]) || typeof data[keys[0]] === 'object')) {
      items = data[keys[0]];
      if (!Array.isArray(items) && typeof items === 'object') {
        const subKeys = Object.keys(items);
        if (subKeys.length === 1 && Array.isArray(items[subKeys[0]])) items = items[subKeys[0]];
      }
    }
    if (!Array.isArray(items)) items = [items];
    if (items.length === 0) {
      container.innerHTML = '<p class="text-center italic">No data found.</p>';
      return;
    }
    items.forEach(item => {
      html += '<div class="border-b pb-2 mb-2 last:border-0">';
      for (const [key, value] of Object.entries(item)) {
        const label = key.charAt(0).toUpperCase() + key.slice(1);
        if (typeof value === 'string' || typeof value === 'number') {
          html += `<p><span class="font-bold text-farm-dark">${label}:</span> ${value}</p>`;
        }
      }
      html += '</div>';
    });
    container.innerHTML = html;
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
  }

  function openNoteModal() {
    // Check auth state before showing the modal
    fetch('php/whoami.php', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
      .then(res => res.json())
      .then(data => {
        if (data && (data.user || data.admin)) {
          const el = document.getElementById('noteModal'); if (el) el.classList.remove('hidden');
        } else {
          // not signed in - open auth modal; set a post-login action to reopen the note modal
          try { sessionStorage.setItem('postLoginAction', 'openNote'); } catch(e){}
          openAuthModal();
        }
      }).catch(err => {
        console.error('whoami check failed', err);
        openAuthModal();
      });
  }

  function initNoteForm() {
    const noteForm = document.getElementById('noteForm');
    if (!noteForm) return;
    noteForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch('php/save-note.php', { method: 'POST', body: formData })
      .then(response => {
        if (!response.ok) {
          return response.json().then(err => { throw { status: response.status, err: err }; });
        }
        return response.json();
      })
      .then(data => {
        if (data && data.success) {
          alert(data.message || "Note saved!");
          closeModal('noteModal');
          this.reset();
          loadNotes();
        } else if (data && data.error) {
          if (data.error.toLowerCase().includes('unauthorized')) {
            // show login modal
            openAuthModal();
          }
          alert(data.error || 'Could not save note.');
        } else {
          alert('Note could not be saved.');
        }
        
      }).catch((e)=>{ 
        if (e && e.err && e.err.error && (e.status === 401 || e.status === 403)) {
          openAuthModal();
          alert(e.err.error);
        } else {
          alert('Could not save note.');
        }
      });
    });
  }

  function loadNotes() {
    fetch('php/load-data.php?file=notes')
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById('notesContainer');
        let html = '';
        let notes = (data && data.note) || [];
        if (!Array.isArray(notes)) notes = [notes];
        notes.forEach(note => {
          const colors = ['border-green-500', 'border-yellow-500', 'border-blue-500'];
          const color = colors[Math.floor(Math.random() * colors.length)];
          const author = note.author || note.name || '';
          html += `\n                        <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 ${color}">\n                            <div class="flex justify-between items-start">\n                              <div>\n                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Note</span>\n                                <h3 class="font-montserrat font-bold text-lg mt-1 mb-2">${note.title}</h3>\n                                <p class="text-gray-600 text-sm">${note.content}</p>\n                              </div>\n                              <div class="text-xs text-gray-400">${author ? 'By ' + author : ''}</div>\n                            </div>\n                        </div>`;
        });
        if(notes.length > 0 && container) container.innerHTML = html;
      })
      .catch(e => console.log('Notes not loaded yet'));
  }

  // --- 3. Auth Logic ---
  function openAuthModal() { const el = document.getElementById('authModal'); if (el) el.classList.remove('hidden'); }

  function switchAuthTab(tab) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const tabLogin = document.getElementById('tabLogin');
    const tabRegister = document.getElementById('tabRegister');

    if (!loginForm || !registerForm || !tabLogin || !tabRegister) return;

    if (tab === 'login') {
      loginForm.classList.remove('hidden');
      registerForm.classList.add('hidden');
      tabLogin.classList.add('border-green-500', 'text-farm-dark');
      tabLogin.classList.remove('border-transparent', 'text-gray-500');
      tabRegister.classList.remove('border-green-500', 'text-farm-dark');
      tabRegister.classList.add('border-transparent', 'text-gray-500');
    } else {
      loginForm.classList.add('hidden');
      registerForm.classList.remove('hidden');
      tabRegister.classList.add('border-green-500', 'text-farm-dark');
      tabRegister.classList.remove('border-transparent', 'text-gray-500');
      tabLogin.classList.remove('border-green-500', 'text-farm-dark');
      tabLogin.classList.add('border-transparent', 'text-gray-500');
    }
  }

  // Mock Submission Handlers
  function initAuthForms() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
      loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(loginForm);
        const btn = loginForm.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.innerText = 'Signing in...'; }
        fetch('php/user-login.php', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
          .then(r => r.json())
          .then(data => {
            if (data && data.success) {
              alert('Welcome ' + (data.user ? data.user.name : 'User') + '! Logged in.');
              loginForm.reset();
              closeModal('authModal');
              // if we stored a post login action, run it
              try {
                const post = sessionStorage.getItem('postLoginAction');
                if (post === 'openNote') {
                  sessionStorage.removeItem('postLoginAction');
                  openNoteModal();
                }
              } catch (e) { /* ignore */ }
              // If admin account, redirect to admin dashboard
              if (data.user && data.user.role && data.user.role.toLowerCase() === 'admin') {
                window.location.href = 'pages/admin.php';
              }
            } else if (data && data.error) {
              alert('Login failed: ' + data.error);
            } else {
              alert('Login failed.');
            }
          }).catch(err => {
            console.error(err);
            alert('Unable to login.');
          }).finally(() => { if (btn) { btn.disabled = false; btn.innerText = 'Sign In'; } });
      });
    }
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
      registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(registerForm);
        // Add role default
        if (!formData.get('role')) formData.set('role', 'User');
        const btn = registerForm.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.innerText = 'Creating...'; }
        fetch('php/register.php', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
          .then(r => r.json())
          .then(data => {
            if (data && data.success) {
              alert('Account created successfully. You can now login.');
              registerForm.reset();
              closeModal('authModal');
            } else if (data && data.error) {
              alert('Registration failed: ' + data.error);
            } else {
              alert('Registration failed.');
            }
          }).catch(err => {
            console.error(err);
            alert('Unable to register. Please try again later.');
          }).finally(() => { if (btn) { btn.disabled = false; btn.innerText = 'Create Account'; } });
      });
    }
  }

  // Expose some functions to global scope as needed for inline onclick handlers
  window.toggleAiChat = toggleAiChat;
  window.askAssistant = askAssistant;
  window.generateAiNewsletter = generateAiNewsletter;
  window.handleMapClick = handleMapClick;
  window.closeModal = closeModal;
  window.openNoteModal = openNoteModal;
  window.openAuthModal = openAuthModal;
  window.switchAuthTab = switchAuthTab;

  // Initialize after DOM loaded
  document.addEventListener('DOMContentLoaded', function () {
    initAiInput();
    initNoteForm();
    initAuthForms();
    // Update Add Note button based on auth state
    fetch('php/whoami.php', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
      .then(res => res.json())
      .then(data => {
        const btn = document.getElementById('btnAddNote');
        if (btn && !(data && (data.user || data.admin))) {
          btn.classList.add('opacity-60');
          btn.title = 'Sign in to add a note';
        }
      }).catch(()=>{});
    // Set up newsletter button
    const draftBtn = document.getElementById('btnGenerateNews');
    if (draftBtn) draftBtn.addEventListener('click', generateAiNewsletter);
    // load any existing notes
    loadNotes();
  });

})();
