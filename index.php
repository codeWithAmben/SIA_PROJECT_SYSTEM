<?php session_start(); ?>
<?php require_once __DIR__ . '/php/csrf.php'; ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XML Farm System + AI</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    
    <?php include __DIR__ . '/php/partials/head.php'; ?>
    <!-- Markdown Parser for AI Responses -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body class="font-poppins bg-farm-light/90 text-gray-800">
    <!-- flash messages moved into header partial -->

    <!-- Navigation -->
    <?php include __DIR__ . '/php/partials/header.php'; ?>

    <!-- Hero Section -->
    <section id="home" class="pt-32 pb-20 px-4 hero-section">
        <div class="max-w-5xl mx-auto text-center hero-inner">
            <h1 class="font-montserrat font-bold text-4xl md:text-6xl text-white mb-6">
                Manage Your Farm with <br> <span class="text-green-600">Smart Farm</span>
            </h1>
            <p class="text-lg text-white/90 mb-8 max-w-2xl mx-auto">
                A simple, secure, and lightweight farm management system.
            <div class="flex justify-center gap-4">
                <a href="#map" class="bg-farm-dark text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:bg-green-800 transition">
                    Explore Map
                </a>
                <button onclick="toggleAiChat()" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-robot"></i> Ask AI Advisor
                </button>
            </div>
        </div>
    </section>

    <!-- Farm Map Section -->
    <section id="map" class="py-20 px-4 bg-white/80 backdrop-blur-sm my-8 rounded-xl max-w-6xl mx-auto">
        <div class="">
            <div class="text-center mb-12">
                <h2 class="font-montserrat font-bold text-3xl text-farm-dark">Interactive Farm Map</h2>
                <p class="text-gray-600 mt-2">Click on the Barn, Field, House, or Pond to view data.</p>
            </div>

            <div class="map-container relative mx-auto">
                <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                     alt="Farm Map" 
                     class="map-image" 
                     usemap="#farmmap"
                     id="farmImage">
                
                <div id="tooltip" class="area-tooltip"></div>

                <map name="farmmap">
                    <area target="" alt="Barn" title="Barn" href="#" coords="0,0,400,300" shape="rect" data-type="animals" onclick="handleMapClick(event, 'animals')">
                    <area target="" alt="Farm House" title="Farm House" href="#" coords="400,0,800,300" shape="rect" data-type="users" onclick="handleMapClick(event, 'users')">
                    <area target="" alt="Crop Field" title="Crop Field" href="#" coords="0,300,400,600" shape="rect" data-type="crops" onclick="handleMapClick(event, 'crops')">
                    <area target="" alt="Storage" title="Storage" href="#" coords="400,300,800,600" shape="rect" data-type="tasks" onclick="handleMapClick(event, 'tasks')">
                </map>

                <div class="absolute top-4 left-4 bg-white/80 p-2 rounded text-xs backdrop-blur-sm">
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-red-400 rounded-full"></div> Top-Left: Animals</div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-blue-400 rounded-full"></div> Top-Right: Admin</div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-green-400 rounded-full"></div> Bottom-Left: Crops</div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-yellow-400 rounded-full"></div> Bottom-Right: Tasks</div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Notes Log Section -->
    <section id="notes" class="py-20 px-4 bg-green-50/90 backdrop-blur-sm">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-8 border-b pb-4 border-gray-200 gap-4">
                <div>
                    <h2 class="font-montserrat font-bold text-3xl text-farm-dark">Daily Note Log</h2>
                    <p class="text-gray-500">Recent entries from <code class="text-sm bg-gray-200 px-1 rounded">notes</code></p>
                </div>
                <div class="flex gap-2">
                    <button onclick="openNoteModal()" id="btnAddNote" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-medium transition shadow-sm">
                        <i class="fa-solid fa-plus mr-1"></i> Add Note
                    </button>
                </div>
            </div>

            <!-- Notes Container -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="notesContainer">
                 <p class="text-gray-500 text-sm italic col-span-3 text-center">Loading notes from notes.xml...</p>
            </div>
        </div>
    </section>

    <!-- Newsletter Section with AI Feature -->
    <section id="newsletter" class="py-20 bg-farm-dark/90 text-white relative overflow-hidden backdrop-blur-sm">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-slow"></div>

        <div class="max-w-4xl mx-auto text-center px-4 relative z-10">
            <i class="fa-regular fa-envelope text-4xl mb-4 text-green-300"></i>
            <h2 class="font-montserrat font-bold text-3xl mb-2">Weekly Farm Update</h2>
            <p class="text-green-100 mb-8">Let Artificial Intelligence write your newsletter based on your weekly notes.</p>
            
            <div class="bg-white/10 backdrop-blur-lg p-6 rounded-xl border border-white/20 mb-8 max-w-2xl mx-auto text-left hidden" id="aiDraftContainer">
                <h3 class="text-green-300 font-bold text-sm uppercase mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Gemini AI Draft
                </h3>
                <div id="aiDraftContent" class="text-gray-100 text-sm italic whitespace-pre-wrap"></div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="generateAiNewsletter()" id="btnGenerateNews" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-3 rounded-full transition shadow-lg border border-blue-400 flex items-center gap-2">
                    <i class="fa-solid fa-sparkles"></i> ✨ Draft with AI
                </button>
                <span class="text-gray-400 text-sm">or</span>
                <form id="newsletterForm" class="flex flex-col sm:flex-row gap-2">
                    <input type="email" name="email" placeholder="Enter subscriber email" class="px-6 py-3 rounded-full text-gray-800 outline-none focus:ring-2 focus:ring-green-400" required>
                    <button type="submit" class="bg-green-500 hover:bg-green-400 text-white font-bold px-6 py-3 rounded-full transition shadow-lg">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/php/partials/footer.php'; ?>

    <!-- --- MODALS --- -->

    <!-- 1. Data Modal (For Map Clicks) -->
    <div id="dataModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('dataModal')"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i id="modalIcon" class="fa-solid fa-wheat-awn text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-xl font-montserrat font-semibold leading-6 text-gray-900" id="modalTitle">Section Title</h3>
                            <div class="mt-4">
                                <div id="modalContent" class="text-sm text-gray-500 space-y-2 bg-gray-50 p-4 rounded border border-gray-100 h-64 overflow-y-auto">
                                    Loading XML data...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto" onclick="closeModal('dataModal')">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. AI Chat Modal (Floating) -->
    <div id="aiChatModal" class="fixed bottom-4 right-4 z-[80] hidden w-80 md:w-96 flex flex-col shadow-2xl rounded-xl overflow-hidden transition-all duration-300 transform translate-y-0">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 p-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-robot"></i>
                <h3 class="font-montserrat font-bold text-sm">Farm Assistant</h3>
            </div>
            <button onclick="toggleAiChat()" class="hover:text-blue-200 transition"><i class="fa-solid fa-minus"></i></button>
        </div>
        <!-- Chat Area -->
        <div id="chatHistory" class="bg-white h-80 overflow-y-auto p-4 chat-scroll flex flex-col gap-3 text-sm">
            <!-- Welcome Msg -->
            <div class="bg-blue-50 p-3 rounded-br-lg rounded-bl-lg rounded-tr-lg self-start max-w-[90%] border border-blue-100 text-gray-700">
                Hello! I'm your Gemini-powered farm assistant. Ask me about crop rotation, pest control, or analyze your notes! ✨
            </div>
        </div>
        <!-- Input Area -->
        <div class="bg-gray-50 p-3 border-t border-gray-200 flex gap-2">
            <input type="text" id="aiInput" class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Ask Gemini...">
            <button onclick="askAssistant()" id="aiSendBtn" class="bg-blue-600 text-white rounded-lg px-3 hover:bg-blue-700 transition">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- 3. Add Note Modal -->
    <div id="noteModal" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity" onclick="closeModal('noteModal')"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="font-montserrat font-bold text-xl mb-4">Add New Note</h3>
                <form id="noteForm" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="title" placeholder="Note Title" class="form-control" required>
                    <textarea name="content" placeholder="Write your note here..." rows="3" class="form-control" required></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeModal('noteModal')" class="text-gray-500 hover:text-gray-700 px-4 py-2">Cancel</button>
                        <button type="submit" class="btn">Save to XML</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. Auth Modal (Login/Register) -->
    <div id="authModal" class="fixed inset-0 z-[90] hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity" onclick="closeModal('authModal')"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-8">
                <!-- Close Button -->
                <button onclick="closeModal('authModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>

                <!-- Tabs -->
                <div class="flex border-b mb-6">
                    <button id="tabLogin" onclick="switchAuthTab('login')" class="flex-1 pb-2 border-b-2 border-green-500 font-bold text-farm-dark transition">Login</button>
                    <button id="tabRegister" onclick="switchAuthTab('register')" class="flex-1 pb-2 border-b-2 border-transparent text-gray-500 hover:text-farm-dark transition">Register</button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="space-y-4" method="post" action="php/user-login.php">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="return" value="../index.php">
                    <h3 class="font-montserrat font-bold text-xl text-center mb-2">Welcome Back</h3>
                    <input type="text" name="email" placeholder="Email or Username" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-400 outline-none" required>
                    <input type="password" name="password" placeholder="Password" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-400 outline-none" required>
                    <button type="submit" class="w-full bg-farm-dark text-white font-bold py-3 rounded-lg hover:bg-green-800 transition shadow-lg">Sign In</button>
                    
                    <div class="relative flex py-2 items-center">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="flex-shrink mx-4 text-gray-400 text-xs">Or continue with</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>
                    
                    <button type="button" class="w-full flex justify-center items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50 font-medium text-sm transition">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google Logo">
                        Google
                    </button>
                </form>

                <!-- Register Form (Hidden by default) -->
                <form id="registerForm" class="space-y-4 hidden" method="post" action="php/register.php">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="return" value="../index.php">
                    <h3 class="font-montserrat font-bold text-xl text-center mb-2">Join GreenAcres</h3>
                    <input type="text" name="name" placeholder="Full Name" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-400 outline-none" required>
                    <input type="email" name="email" placeholder="Email Address" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-400 outline-none" required>
                    <input type="password" name="password" placeholder="Create Password" class="w-full border p-3 rounded-lg focus:ring-2 focus:ring-green-400 outline-none" required>
                    <button type="submit" class="w-full bg-farm-green text-white font-bold py-3 rounded-lg hover:bg-green-500 transition shadow-lg">Create Account</button>
                    <p class="text-xs text-center text-gray-500">By registering, you agree to our Terms & Conditions.</p>
                </form>
            </div>
        </div>
    </div>

    <!-- Load main script (app logic in external file) -->
    <script src="js/main.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            try{
                const params = new URLSearchParams(window.location.search);
                if (params.get('auth') === '1'){
                    const ret = params.get('return') ? params.get('return') : null;
                    const loginReturn = document.querySelector('#loginForm input[name="return"]');
                    const registerReturn = document.querySelector('#registerForm input[name="return"]');
                    if (ret && loginReturn) loginReturn.value = ret;
                    if (ret && registerReturn) registerReturn.value = ret;
                    if (typeof openAuthModal === 'function') openAuthModal();
                }
            }catch(e){console.error(e)}
        });
    </script>

</body>
</html>