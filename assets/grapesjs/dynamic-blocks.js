(function () {
    function registerDynamicBlocks(editor) {
        editor.BlockManager.add('site-menu', {
            label: 'Site Menu',
            category: 'Dynamic',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="6" width="42" height="8" rx="1" stroke-dasharray="3 3"/><line x1="7" y1="10" x2="14" y2="10"/><line x1="18" y1="10" x2="25" y2="10"/><line x1="29" y1="10" x2="36" y2="10"/></svg>',
            content: '<div class="hf-dynamic-block hf-site-menu" style="padding:15px;border:2px dashed #999;background:#f8f9fa;text-align:center;color:#666;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif;">{{SITE_MENU}}</div>',
        });

        editor.BlockManager.add('site-name', {
            label: 'Site Name',
            category: 'Dynamic',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="14" width="42" height="8" rx="1" stroke-dasharray="3 3"/><text x="24" y="20" font-size="7" fill="currentColor" stroke="none" text-anchor="middle">Aa</text></svg>',
            content: '<span class="hf-dynamic-block hf-site-name" style="padding:4px 10px;border:2px dashed #999;background:#f8f9fa;color:#666;">{{SITE_NAME}}</span>',
        });

        editor.BlockManager.add('copyright-notice', {
            label: 'Copyright Notice',
            category: 'Dynamic',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="14" width="42" height="8" rx="1" stroke-dasharray="3 3"/><circle cx="12" cy="18" r="3"/><line x1="20" y1="18" x2="40" y2="18"/></svg>',
            content: '<div class="hf-dynamic-block hf-copyright" style="padding:8px 15px;border:2px dashed #999;background:#f8f9fa;color:#666;font-size:0.85rem;">&copy; {{COPYRIGHT_YEAR}} {{SITE_NAME}}. All rights reserved.</div>',
        });
    }

    window.registerDynamicBlocks = registerDynamicBlocks;
})();
