</div>
<script>

<?php if (isset($this->security) && $this->security->get_csrf_hash()): ?>
var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
$.ajaxSetup({
    data: function(d) {
        if (typeof d === 'object') { d[csrfName] = csrfHash; return d; }
        return d + '&' + csrfName + '=' + csrfHash;
    }(),
    cache: false
});
<?php endif; ?>
</script>
</body>
</html>
