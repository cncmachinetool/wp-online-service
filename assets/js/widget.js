(function ($) {
    const state = {
        open: false,
    };

    function togglePanel() {
        const panel = $('.wp-online-service__panel');
        const toggle = $('.wp-online-service__toggle');

        state.open = !state.open;
        toggle.attr('aria-expanded', state.open);
        panel.attr('hidden', !state.open);
    }

    function bindChannels() {
        const channels = wpOnlineService.channels || {};

        $('[data-channel="whatsapp"]').on('click', function () {
            if (!channels.whatsapp) {
                return;
            }
            const url = this.href || buildWhatsAppUrl(channels.whatsapp);
            window.open(url, '_blank', 'noopener');
        });

        $('[data-channel="wechat"]').on('click', function () {
            const value = $(this).data('clipboard');
            if (!value) {
                return;
            }

            copyToClipboard(value);
            const label = wpOnlineService?.i18n?.copyWeChat || 'Copied';
            $(this).addClass('is-copied');
            setTimeout(() => $(this).removeClass('is-copied'), 1500);
            alert(label);
        });
    }

    function buildWhatsAppUrl(number) {
        const digits = String(number).replace(/\D/g, '');
        const text = encodeURIComponent(wpOnlineService?.i18n?.welcome || 'Hello!');
        return `https://wa.me/${digits}?text=${text}`;
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
            return;
        }

        const temp = document.createElement('textarea');
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
    }

    $(function () {
        $('.wp-online-service__toggle').on('click', togglePanel);
        bindChannels();
    });
})(jQuery);
