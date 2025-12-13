# WP Online Service

一个适合外贸企业国际站点的 WordPress 在线客服插件，提供悬浮式客服入口，支持 WhatsApp、Email、电话与微信（支持二维码弹窗）等多种联系方式，并可在后台自定义公司与客服信息。

## 特性
- 悬浮式呼叫入口：一键展开/收起，提升访客转化。
- 多渠道触达：WhatsApp、邮件、电话、微信 ID 复制/二维码扫码。
- 国际友好：按钮文案、默认欢迎语适合跨境采购场景，可根据需要自行翻译。
- 后台配置：可在 “设置 > Online Service” 中管理公司名、客服名、工作时间、CTA 文案及联系方式。

## 安装与使用
1. 将本仓库内容上传至 WordPress 插件目录（如 `wp-content/plugins/wp-online-service`）。
2. 在 WordPress 后台启用 “WP Online Service”。
3. 进入 “设置 > Online Service”，填写 WhatsApp 号码、邮箱、电话或微信 ID，并可添加微信二维码图片地址后保存。
4. 前台将自动出现悬浮客服按钮，点击后可快速触达对应渠道。

## 开发提示
- 样式位于 `assets/css/widget.css`，可根据品牌色调整渐变与阴影。
- 前端交互逻辑位于 `assets/js/widget.js`，使用 jQuery 处理展开、复制及渠道点击。
- 若需多语言，请在 `languages/` 下添加对应的 `.po/.mo` 文件并更新文案。
