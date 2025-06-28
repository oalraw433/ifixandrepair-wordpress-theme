<script>
        // WordPress AJAX configuration
        const repairFormAjax = {
            ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('repair_form_nonce'); ?>'
        };
    </script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/customer-intake.js"></script>
    <?php wp_footer(); ?>
</body>
</html>