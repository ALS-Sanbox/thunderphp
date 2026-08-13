<?php
$footerLayout = setting('footer_layout');
$hasFooterLayout = is_array($footerLayout) && !empty($footerLayout['html']);

if ($hasFooterLayout) {
    $footerTokens = ['{{SITE_NAME}}', '{{COPYRIGHT_YEAR}}'];
    $footerValues = [esc(APP_NAME), date('Y')];

    echo '<style>' . ($footerLayout['css'] ?? '') . '</style>';
    echo str_replace($footerTokens, $footerValues, $footerLayout['html']);
} else {
?>
<footer class="text-center py-3">
    <p>&copy; <?=date("Y")?> <?=APP_NAME?>. All rights reserved.</p>
</footer>
<?php
}
?>
<script src="<?=ROOT?>/assets/scripts/bootstrap.min.js"></script>
</body>
</html>
