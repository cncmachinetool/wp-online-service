(function ($) {
    const state = {
        open: true,
    };

    function syncPanel() {
        const panel = $('.wp-online-service__panel');
        const toggle = $('.wp-online-service__toggle');

        toggle.attr('aria-expanded', state.open);
        panel.prop('hidden', !state.open);
    }

    function togglePanel() {
        const panel = $('.wp-online-service__panel');
        const toggle = $('.wp-online-service__toggle');

        state.open = !state.open;
        toggle.attr('aria-expanded', state.open);
        panel.prop('hidden', !state.open);
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

        bindWechat();
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

    function bindWechat() {
        const modal = $('#wp-online-service-wechat');
        const trigger = $('[data-channel="wechat"]');
        const clipboardValue = trigger.data('clipboard');

        if (modal.length) {
            const backdrop = modal.find('.wp-online-service__modal-backdrop');
            const close = modal.find('.wp-online-service__modal-close');

            const openModal = () => {
                modal.addClass('is-visible').prop('hidden', false);
                $('body').addClass('wp-online-service--modal-open');
            };

            const closeModal = () => {
                modal.removeClass('is-visible').prop('hidden', true);
                $('body').removeClass('wp-online-service--modal-open');
            };

            trigger.on('click', function (e) {
                e.preventDefault();
                openModal();
            });

            backdrop.on('click', closeModal);
            close.on('click', closeModal);
            $(document).on('keyup', function (e) {
                if (e.key === 'Escape' && modal.hasClass('is-visible')) {
                    closeModal();
                }
            });
        } else if (clipboardValue) {
            trigger.on('click', function () {
                copyToClipboard(clipboardValue);
                const label = wpOnlineService?.i18n?.copyWeChat || 'Copied';
                $(this).addClass('is-copied');
                setTimeout(() => $(this).removeClass('is-copied'), 1500);
                alert(label);
            });
        }
    }

    $(function () {
        $('.wp-online-service__toggle').on('click', togglePanel);
        syncPanel();
        bindChannels();
    });
})(jQuery);
