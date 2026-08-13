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

        editor.BlockManager.add('site-logo', {
            label: 'Logo',
            category: 'Dynamic',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="10" y="6" width="28" height="24" rx="2" stroke-dasharray="3 3"/><circle cx="19" cy="15" r="3"/><path d="M12 26l7-7 5 5 4-4 8 8"/></svg>',
            content: '<div class="hf-dynamic-block hf-site-logo" style="padding:15px;border:2px dashed #999;background:#f8f9fa;text-align:center;color:#666;">{{SITE_LOGO}}</div>',
        });

        editor.BlockManager.add('home-link', {
            label: 'Home Link',
            category: 'Dynamic',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 18L24 8l14 10"/><path d="M13 16v12h22V16" stroke-dasharray="3 3"/></svg>',
            content: '<span class="hf-dynamic-block hf-home-link" style="padding:4px 10px;border:2px dashed #999;background:#f8f9fa;color:#666;">{{HOME_LINK}}</span>',
        });

        editor.BlockManager.add('user-menu', {
            label: 'User Menu',
            category: 'Dynamic',
            media: '<svg viewBox="0 0 48 36" width="48" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="10" width="42" height="16" rx="1" stroke-dasharray="3 3"/><circle cx="13" cy="18" r="4"/><line x1="22" y1="15" x2="38" y2="15"/><line x1="22" y1="21" x2="34" y2="21"/></svg>',
            content: '<div class="hf-dynamic-block hf-user-menu-block" style="padding:15px;border:2px dashed #999;background:#f8f9fa;text-align:center;color:#666;">{{USER_MENU}}</div>',
        });
    }

    window.registerDynamicBlocks = registerDynamicBlocks;
})();
