<?php
/**
 * Plugin Name: WP Online Service
 * Description: Provides a floating multilingual-friendly online service widget tailored for international trade websites, with quick links to WhatsApp, email, phone, and WeChat contact options.
 * Version: 1.0.0
 * Author: WP Online Service Team
 * License: GPL-2.0-or-later
 * Text Domain: wp-online-service
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Online_Service {
    private $options_key = 'wp_online_service_options';

    public function __construct() {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_action('admin_menu', [$this, 'register_admin_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render_widget']);
    }

    public function load_textdomain() {
        load_plugin_textdomain('wp-online-service', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function register_admin_page() {
        add_options_page(
            __('Online Service', 'wp-online-service'),
            __('Online Service', 'wp-online-service'),
            'manage_options',
            'wp-online-service',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting($this->options_key, $this->options_key, [
            'sanitize_callback' => [$this, 'sanitize_options'],
        ]);

        add_settings_section(
            'wp_online_service_section_company',
            __('Company & Agent Info', 'wp-online-service'),
            '__return_false',
            'wp-online-service'
        );

        $fields = [
            'company_name' => __('Company name', 'wp-online-service'),
            'agent_name' => __('Service agent name', 'wp-online-service'),
            'tagline' => __('Short tagline', 'wp-online-service'),
            'availability' => __('Availability (e.g. 24/7 or UTC+8 9:00-18:00)', 'wp-online-service'),
        ];

        foreach ($fields as $key => $label) {
            add_settings_field(
                $key,
                $label,
                [$this, 'render_text_field'],
                'wp-online-service',
                'wp_online_service_section_company',
                [
                    'key' => $key,
                    'label_for' => $key,
                ]
            );
        }

        add_settings_section(
            'wp_online_service_section_channels',
            __('Contact channels', 'wp-online-service'),
            '__return_false',
            'wp-online-service'
        );

        $channel_fields = [
            'whatsapp_number' => __('WhatsApp number (international format)', 'wp-online-service'),
            'wechat_id' => __('WeChat ID', 'wp-online-service'),
            'email' => __('Support email', 'wp-online-service'),
            'phone' => __('Phone number', 'wp-online-service'),
            'cta_text' => __('Primary call-to-action text', 'wp-online-service'),
        ];

        foreach ($channel_fields as $key => $label) {
            add_settings_field(
                $key,
                $label,
                [$this, 'render_text_field'],
                'wp-online-service',
                'wp_online_service_section_channels',
                [
                    'key' => $key,
                    'label_for' => $key,
                ]
            );
        }
    }

    public function sanitize_options($input) {
        $output = [];
        $allowed_keys = [
            'company_name',
            'agent_name',
            'tagline',
            'availability',
            'whatsapp_number',
            'wechat_id',
            'email',
            'phone',
            'cta_text',
        ];

        foreach ($allowed_keys as $key) {
            if (!isset($input[$key])) {
                continue;
            }

            switch ($key) {
                case 'email':
                    $output[$key] = sanitize_email($input[$key]);
                    break;
                case 'tagline':
                case 'availability':
                case 'company_name':
                case 'agent_name':
                case 'cta_text':
                    $output[$key] = sanitize_text_field($input[$key]);
                    break;
                case 'phone':
                case 'wechat_id':
                case 'whatsapp_number':
                    $output[$key] = preg_replace('/[^0-9+\-\s]/', '', $input[$key]);
                    break;
            }
        }

        return $output;
    }

    public function get_options() {
        $defaults = [
            'company_name' => __('Your Company', 'wp-online-service'),
            'agent_name' => __('Online Specialist', 'wp-online-service'),
            'tagline' => __('Export & logistics support for global buyers', 'wp-online-service'),
            'availability' => __('We respond within 5 minutes on business days.', 'wp-online-service'),
            'whatsapp_number' => '',
            'wechat_id' => '',
            'email' => '',
            'phone' => '',
            'cta_text' => __('Chat with us', 'wp-online-service'),
        ];

        $saved = get_option($this->options_key, []);

        return wp_parse_args($saved, $defaults);
    }

    public function render_text_field($args) {
        $options = $this->get_options();
        $key = $args['key'];
        $value = isset($options[$key]) ? $options[$key] : '';
        printf(
            '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" />',
            esc_attr($key),
            esc_attr($this->options_key),
            esc_attr($value)
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Online Service Widget', 'wp-online-service'); ?></h1>
            <p><?php esc_html_e('Configure the floating customer service widget for your international visitors.', 'wp-online-service'); ?></p>
            <form method="post" action="options.php">
                <?php
                    settings_fields($this->options_key);
                    do_settings_sections('wp-online-service');
                    submit_button(__('Save settings', 'wp-online-service'));
                ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_assets() {
        if (is_admin()) {
            return;
        }

        $plugin_url = plugin_dir_url(__FILE__);
        wp_enqueue_style('wp-online-service-widget', $plugin_url . 'assets/css/widget.css', [], '1.0.0');
        wp_enqueue_script('wp-online-service-widget', $plugin_url . 'assets/js/widget.js', ['jquery'], '1.0.0', true);

        $options = $this->get_options();

        wp_localize_script('wp-online-service-widget', 'wpOnlineService', [
            'company' => $options['company_name'],
            'agent' => $options['agent_name'],
            'tagline' => $options['tagline'],
            'availability' => $options['availability'],
            'cta' => $options['cta_text'],
            'channels' => [
                'whatsapp' => $options['whatsapp_number'],
                'wechat' => $options['wechat_id'],
                'email' => $options['email'],
                'phone' => $options['phone'],
            ],
            'i18n' => [
                'whatsapp' => __('WhatsApp', 'wp-online-service'),
                'wechat' => __('WeChat', 'wp-online-service'),
                'email' => __('Email', 'wp-online-service'),
                'phone' => __('Call', 'wp-online-service'),
                'copyWeChat' => __('WeChat ID copied', 'wp-online-service'),
                'offline' => __('We will reply soon', 'wp-online-service'),
                'welcome' => __('Hello! How can we assist your sourcing today?', 'wp-online-service'),
            ],
        ]);
    }

    public function render_widget() {
        if (is_admin()) {
            return;
        }

        $options = $this->get_options();
        ?>
        <div class="wp-online-service" aria-live="polite">
            <button class="wp-online-service__toggle" aria-expanded="false" aria-controls="wp-online-service-panel">
                <span class="wp-online-service__cta"><?php echo esc_html($options['cta_text']); ?></span>
                <span class="wp-online-service__badge"><?php echo esc_html($options['agent_name']); ?></span>
            </button>
            <div class="wp-online-service__panel" id="wp-online-service-panel" hidden>
                <header class="wp-online-service__header">
                    <div class="wp-online-service__title"><?php echo esc_html($options['company_name']); ?></div>
                    <div class="wp-online-service__meta">
                        <strong><?php echo esc_html($options['agent_name']); ?></strong>
                        <span><?php echo esc_html($options['tagline']); ?></span>
                        <small><?php echo esc_html($options['availability']); ?></small>
                    </div>
                </header>
                <div class="wp-online-service__body">
                    <?php if (!empty($options['whatsapp_number'])) : ?>
                        <a class="wp-online-service__channel" data-channel="whatsapp" href="<?php echo esc_url($this->build_whatsapp_url($options['whatsapp_number'])); ?>" target="_blank" rel="noopener">
                            <span class="wp-online-service__channel-label"><?php esc_html_e('WhatsApp', 'wp-online-service'); ?></span>
                            <small><?php echo esc_html($options['whatsapp_number']); ?></small>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($options['email'])) : ?>
                        <a class="wp-online-service__channel" data-channel="email" href="<?php echo esc_url('mailto:' . $options['email']); ?>">
                            <span class="wp-online-service__channel-label"><?php esc_html_e('Email', 'wp-online-service'); ?></span>
                            <small><?php echo esc_html($options['email']); ?></small>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($options['phone'])) : ?>
                        <a class="wp-online-service__channel" data-channel="phone" href="<?php echo esc_url('tel:' . $options['phone']); ?>">
                            <span class="wp-online-service__channel-label"><?php esc_html_e('Call', 'wp-online-service'); ?></span>
                            <small><?php echo esc_html($options['phone']); ?></small>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($options['wechat_id'])) : ?>
                        <button class="wp-online-service__channel" data-channel="wechat" type="button" data-clipboard="<?php echo esc_attr($options['wechat_id']); ?>">
                            <span class="wp-online-service__channel-label"><?php esc_html_e('WeChat', 'wp-online-service'); ?></span>
                            <small><?php echo esc_html($options['wechat_id']); ?></small>
                        </button>
                    <?php endif; ?>
                </div>
                <footer class="wp-online-service__footer">
                    <p><?php esc_html_e('Instant support for quotations, samples, and shipping.', 'wp-online-service'); ?></p>
                </footer>
            </div>
        </div>
        <?php
    }

    private function build_whatsapp_url($number) {
        $text = rawurlencode(__('Hello! I would like to learn more about your products.', 'wp-online-service'));
        $clean = preg_replace('/[^0-9]/', '', $number);
        return sprintf('https://wa.me/%s?text=%s', $clean, $text);
    }
}

new WP_Online_Service();
