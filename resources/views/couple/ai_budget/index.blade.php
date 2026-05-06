@extends('couple.layout.layout-couple')

@section('title', 'AI Estimate Budget - WebPlan')
@section('page-title', 'AI Estimate Budget')
@section('page-subtitle', 'Get personalized wedding budget recommendations from our AI assistant.')

@push('page-styles')
    @vite(['resources/css/couple/ai-budget.css'])
@endpush

@section('content')
<div class="ai-budget-page">
    <div class="budget-hero">
        <div class="budget-hero-top">
            <div>
                <span class="budget-kicker">AI Budget Assistant</span>
                <h1 class="budget-title">AI Estimate Budget</h1>
                <p class="budget-subtitle">Get personalized wedding budget recommendations from our AI assistant.</p>
            </div>
            <div class="budget-presence" id="budgetPresence" data-status="online">
                <span class="budget-presence-dot"></span>
                <div>
                    <strong>Chat online</strong>
                    <span id="onlineStatus">Ready to estimate</span>
                </div>
            </div>
        </div>
    </div>

    <div class="ai-budget-container">
        <div class="ai-chat-wrapper">
            <div class="ai-chat-header">
                <div class="ai-chat-avatar" id="chatAvatar" data-status="online">AI</div>
                <div class="ai-chat-meta">
                    <strong>Budget Assistant</strong>
                    <span id="chatStatusText">Online and ready</span>
                </div>
                <button type="button" class="ai-chat-add" aria-label="More options">
                    <span>+</span>
                </button>
            </div>

            <!-- Messages Container -->
            <div class="ai-chat-messages" id="chatMessages">
                <div class="ai-message ai-message-system" id="initialQuestion">
                    <div class="ai-message-content">
                        <div class="ai-bubble-topline">Hi, I’m your wedding budget bot.</div>
                        <p>Tell me your guest count and budget range, and I’ll calculate a helpful estimate.</p>
                        <div class="ai-chat-quickstart">
                            <button type="button" class="ai-quick-chip" data-guest="120" data-budget="RM 10000 - RM 20000">120 guests</button>
                            <button type="button" class="ai-quick-chip" data-guest="200" data-budget="RM 2500 - RM 40000">200 guests</button>
                            <button type="button" class="ai-quick-chip" data-guest="300" data-budget="RM 50000 And Above">300 guests</button>
                            <button type="button" class="ai-quick-chip" data-guest="80" data-budget="None Of Above">Not sure yet</button>
                        </div>
                        <div class="ai-question-form">
                            <div class="form-group">
                                <label for="guestCount" class="form-label">Guest count</label>
                                <input 
                                    type="number" 
                                    id="guestCount" 
                                    class="form-input" 
                                    placeholder="e.g., 150" 
                                    min="1" 
                                    max="10000"
                                    value=""
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label">Budget range</label>
                                <div class="budget-options">
                                    <button type="button" class="budget-option-btn" data-budget="RM 10000 - RM 20000">
                                        <span class="budget-range">RM 10,000 - RM 20,000</span>
                                    </button>
                                    <button type="button" class="budget-option-btn" data-budget="RM 2500 - RM 40000">
                                        <span class="budget-range">RM 25,000 - RM 40,000</span>
                                    </button>
                                    <button type="button" class="budget-option-btn" data-budget="RM 50000 And Above">
                                        <span class="budget-range">RM 50,000+</span>
                                    </button>
                                    <button type="button" class="budget-option-btn" data-budget="None Of Above">
                                        <span class="budget-range">Not Sure Yet</span>
                                    </button>
                                </div>
                            </div>

                            <button type="button" class="ai-submit-btn" id="submitQuestionnaireBtn">
                                <span>Get My Budget Estimate</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div class="ai-loading-state" id="loadingState" style="display: none;">
                <div class="ai-loading-spinner">
                    <div class="spinner"></div>
                    <p>Analyzing your wedding budget...</p>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="ai-chat-input-section" id="chatInputSection" style="display: none;">
                <form class="ai-chat-form" id="chatForm" onsubmit="return false;">
                    <div class="ai-chat-input-wrapper">
                        <input 
                            type="text" 
                            id="messageInput" 
                            class="ai-chat-input" 
                            placeholder="Ask me anything about your wedding budget..."
                            autocomplete="off"
                        >
                        <button type="button" class="ai-send-btn" id="sendBtn" aria-label="Send message">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16.6915026,12.4744748 L3.50612381,13.2599618 C3.19218622,13.2599618 3.03521743,13.4170592 3.03521743,13.5741566 L1.15159189,20.0151496 C0.8376543,20.8006365 0.99,21.89 1.77946707,22.52 C2.41,22.99 3.50612381,23.1 4.13399899,22.8429026 L21.714504,14.0454487 C22.6563168,13.5741566 23.1272231,12.6315722 22.9702544,11.6889879 C22.9702544,11.6889879 22.9702544,11.6889879 22.9702544,11.5318905 L4.13399899,1.16346272 C3.34915502,0.9 2.40734225,1.00636533 1.77946707,1.4776575 C0.994623095,2.10604706 0.837654326,3.0486314 1.15159189,3.99039575 L3.03521743,10.4313888 C3.03521743,10.5884861 3.19218622,10.7455835 3.50612381,10.7455835 L16.6915026,11.5318905 C16.6915026,11.5318905 17.1624089,11.5318905 17.1624089,12.0031827 C17.1624089,12.4744748 16.6915026,12.4744748 16.6915026,12.4744748 Z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="ai-chat-hints">
                        <span>Tips: Ask about budget breakdown, money-saving ideas, or specific categories</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('page-scripts')
    <script>
        let selectedBudgetRange = null;
        let currentGuestCount = null;
        let isOnline = true;

        const chatAvatar = document.getElementById('chatAvatar');
        const chatStatusText = document.getElementById('chatStatusText');
        const onlineStatus = document.getElementById('onlineStatus');
        const budgetPresence = document.getElementById('budgetPresence');

        document.querySelectorAll('.budget-option-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.budget-option-btn').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                selectedBudgetRange = this.dataset.budget;
            });
        });

        document.querySelectorAll('.ai-quick-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                const guestCount = this.dataset.guest;
                const budgetRange = this.dataset.budget;

                document.getElementById('guestCount').value = guestCount;
                document.querySelectorAll('.budget-option-btn').forEach(button => {
                    button.classList.toggle('selected', button.dataset.budget === budgetRange);
                });
                selectedBudgetRange = budgetRange;
            });
        });

        function setAssistantStatus(online) {
            isOnline = online;
            if (chatAvatar) {
                chatAvatar.dataset.status = online ? 'online' : 'offline';
                chatAvatar.textContent = online ? 'AI' : 'OFF';
            }

            if (chatStatusText) {
                chatStatusText.textContent = online ? 'Online and ready' : 'Offline - Chat unavailable';
            }

            if (budgetPresence) {
                budgetPresence.dataset.status = online ? 'online' : 'offline';
                const presenceStrong = budgetPresence.querySelector('strong');

                if (presenceStrong) {
                    presenceStrong.textContent = online ? 'Chat online' : 'Chat offline';
                }

                if (onlineStatus) {
                    onlineStatus.textContent = online ? 'Ready to estimate' : 'Connection lost';
                }
            }
        }

        document.getElementById('submitQuestionnaireBtn').addEventListener('click', async function() {
            const guestCount = document.getElementById('guestCount').value;

            if (!guestCount || guestCount < 1) {
                alert('Please enter the number of guests');
                return;
            }

            if (!selectedBudgetRange) {
                alert('Please select a budget range');
                return;
            }

            currentGuestCount = guestCount;

            // Show loading state
            document.getElementById('initialQuestion').style.display = 'none';
            document.getElementById('loadingState').style.display = 'block';

            try {
                const response = await fetch('{{ route("couple.ai.budget-estimation.estimate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        guest_count: parseInt(guestCount),
                        budget_range: selectedBudgetRange
                    })
                });

                const data = await response.json();

                document.getElementById('loadingState').style.display = 'none';

                if (data.success) {
                    setAssistantStatus(true);
                    addAIMessage(data.message);
                    document.getElementById('chatInputSection').style.display = 'block';
                    document.getElementById('messageInput').focus();
                } else {
                    if (data.offline) {
                        setAssistantStatus(false);
                    }
                    addErrorMessage(data.error || 'Failed to generate budget estimate');
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('loadingState').style.display = 'none';
                setAssistantStatus(false);
                addErrorMessage('Something went wrong. Please try again.');
            }
        });

        document.getElementById('sendBtn').addEventListener('click', sendMessage);
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        async function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();

            if (!message) return;

            // Add user message to chat
            addUserMessage(message);
            messageInput.value = '';

            // Show loading indicator
            addLoadingMessage();

            try {
                const response = await fetch('{{ route("couple.ai.budget-estimation.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        message: message,
                        guest_count: currentGuestCount,
                        budget_range: selectedBudgetRange
                    })
                });

                const data = await response.json();

                // Remove loading message
                removeLoadingMessage();

                if (data.success) {
                    setAssistantStatus(true);
                    addAIMessage(data.message);
                } else {
                    if (data.offline) {
                        setAssistantStatus(false);
                    }
                    addErrorMessage(data.error || 'Failed to get response');
                }
            } catch (error) {
                console.error('Error:', error);
                removeLoadingMessage();
                setAssistantStatus(false);
                addErrorMessage('Something went wrong. Please try again.');
            }
        }

        function addUserMessage(message) {
            const messagesContainer = document.getElementById('chatMessages');
            const messageEl = document.createElement('div');
            messageEl.className = 'ai-message ai-message-user';
            messageEl.innerHTML = `<div class="ai-message-content"><p>${escapeHtml(message)}</p></div>`;
            messagesContainer.appendChild(messageEl);
            scrollToBottom();
        }

        function addAIMessage(message) {
            const messagesContainer = document.getElementById('chatMessages');
            const messageEl = document.createElement('div');
            messageEl.className = 'ai-message ai-message-ai';
            messageEl.innerHTML = `<div class="ai-message-content"><p>${formatMessage(message)}</p></div>`;
            messagesContainer.appendChild(messageEl);
            scrollToBottom();
        }

        function addErrorMessage(message) {
            const messagesContainer = document.getElementById('chatMessages');
            const messageEl = document.createElement('div');
            messageEl.className = 'ai-message ai-message-error';
            messageEl.innerHTML = `<div class="ai-message-content"><p>⚠️ ${escapeHtml(message)}</p></div>`;
            messagesContainer.appendChild(messageEl);
            scrollToBottom();
        }

        function addLoadingMessage() {
            const messagesContainer = document.getElementById('chatMessages');
            const messageEl = document.createElement('div');
            messageEl.className = 'ai-message ai-message-loading';
            messageEl.id = 'loadingMessage';
            messageEl.innerHTML = `<div class="ai-message-content"><div class="typing-indicator"><span></span><span></span><span></span></div></div>`;
            messagesContainer.appendChild(messageEl);
            scrollToBottom();
        }

        function removeLoadingMessage() {
            const loadingMsg = document.getElementById('loadingMessage');
            if (loadingMsg) loadingMsg.remove();
        }

        function scrollToBottom() {
            const messagesContainer = document.getElementById('chatMessages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatMessage(text) {
            return text
                .split('\n')
                .map((line, index) => {
                    if (line.trim().match(/^\d+\./)) {
                        return `<strong>${escapeHtml(line)}</strong>`;
                    }
                    return escapeHtml(line);
                })
                .join('<br>');
        }
    </script>
@endpush
