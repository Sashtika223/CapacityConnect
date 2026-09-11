@extends('layouts.trainee')

@section('title', 'AI Learning Assistant')

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; height: calc(100vh - 140px);">
    <!-- Chat Header -->
    <div style="background: #1e293b; padding: 20px 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #2563eb, #7c3aed); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 4px 12px rgba(37,99,235,0.4);">
                🤖
            </div>
            <div>
                <h1 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 2px 0;">AI Capacity Companion</h1>
                <p style="font-size: 12px; color: #94a3b8; margin: 0;">Instant guidance on emergency management, disaster response protocols, and course study.</p>
            </div>
        </div>

        <span class="badge" style="background: rgba(16,185,129,0.15); color: #34d399; font-size: 11px; font-weight: 600;">
            ● Online & Ready
        </span>
    </div>

    <!-- Suggested Prompts Chips -->
    <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px;">
        <button onclick="sendQuickPrompt('What are the standard ICS incident command steps for flood evacuation?')" style="background: #1e293b; border: 1px solid #334155; color: #cbd5e1; padding: 8px 14px; border-radius: 20px; font-size: 12px; white-space: nowrap; cursor: pointer;">
            🌊 Flood Evacuation Protocol
        </button>
        <button onclick="sendQuickPrompt('Explain the difference between hazard assessment and risk radar.')" style="background: #1e293b; border: 1px solid #334155; color: #cbd5e1; padding: 8px 14px; border-radius: 20px; font-size: 12px; white-space: nowrap; cursor: pointer;">
            ⚡ Hazard Assessment vs Risk Radar
        </button>
        <button onclick="sendQuickPrompt('How do I prepare for my upcoming practical assessment?')" style="background: #1e293b; border: 1px solid #334155; color: #cbd5e1; padding: 8px 14px; border-radius: 20px; font-size: 12px; white-space: nowrap; cursor: pointer;">
            📝 Assessment Prep Advice
        </button>
    </div>

    <!-- Chat Messages Window -->
    <div id="chat-messages" style="flex: 1; background: #0f172a; border-radius: 14px; border: 1px solid #334155; padding: 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px;">
        <!-- Bot Welcome Message -->
        <div style="display: flex; gap: 12px; max-width: 80%;">
            <div style="width: 32px; height: 32px; background: #2563eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">🤖</div>
            <div style="background: #1e293b; padding: 14px 18px; border-radius: 12px; color: #f8fafc; font-size: 14px; line-height: 1.5; border: 1px solid #334155;">
                Hello {{ Auth::user()->name }}! I am your AI Capacity Companion. Ask me anything about your enrolled courses, disaster management concepts, or emergency response strategies.
            </div>
        </div>
    </div>

    <!-- Chat Input Form -->
    <form id="chat-form" style="display: flex; gap: 12px;">
        <input type="text" id="chat-input" placeholder="Ask a question about disaster management or your courses..." required style="flex: 1; background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 14px 18px; color: #fff; font-size: 14px; outline: none;">
        <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 0 24px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
            Send ➔
        </button>
    </form>
</div>

@push('scripts')
<script>
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    function escapeHtml(value) {
        return String(value).replace(/[&<>'"]/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#039;',
            '"': '&quot;'
        })[character]);
    }

    function appendMessage(sender, text, time) {
        const isUser = sender === 'user';
        const wrapper = document.createElement('div');
        wrapper.style.display = 'flex';
        wrapper.style.gap = '12px';
        wrapper.style.justifyContent = isUser ? 'flex-end' : 'flex-start';
        wrapper.style.maxWidth = '100%';

        const html = `
            ${!isUser ? '<div class="chat-avatar chat-avatar-bot">🤖</div>' : ''}
            <div class="chat-bubble ${isUser ? 'chat-bubble-user' : 'chat-bubble-bot'}">
                ${escapeHtml(text).replace(/\n/g, '<br>')}
                <div class="chat-time ${isUser ? 'chat-time-user' : ''}">${time || 'Just now'}</div>
            </div>
            ${isUser ? '<div class="chat-avatar chat-avatar-user">{{ substr(Auth::user()->name, 0, 2) }}</div>' : ''}
        `;
        wrapper.innerHTML = html;
        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function sendQuickPrompt(prompt) {
        chatInput.value = prompt;
        chatForm.dispatchEvent(new Event('submit'));
    }

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;

        appendMessage('user', message);
        chatInput.value = '';

        const sendButton = chatForm.querySelector('button[type="submit"]');
        sendButton.disabled = true;
        sendButton.textContent = 'Sending...';

        try {
            const res = await fetch("{{ route('trainee.ai.chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            });

            const data = await res.json();
            if (!res.ok || !data.answer) throw new Error(data.message || 'Assistant request failed');
            appendMessage('bot', data.answer, data.timestamp);
        } catch (err) {
            appendMessage('bot', 'The assistant is temporarily unavailable. Try /help or ask your trainer for official guidance.');
        } finally {
            sendButton.disabled = false;
            sendButton.textContent = 'Send ➔';
            chatInput.focus();
        }
    });
</script>
@endpush
@endsection
