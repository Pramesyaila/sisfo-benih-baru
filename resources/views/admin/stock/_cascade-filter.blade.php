<script>
    (function () {
        var form = document.getElementById('stock-filter');
        if (!form) return;

        var category = form.querySelector('select[name="category"]');
        var variety = form.querySelector('select[name="variety"]');
        var grade = form.querySelector('select[name="grade"]');
        var gradeWrap = form.querySelector('[data-grade-wrap]');

        function toggleOptions(select, isVisible) {
            Array.prototype.forEach.call(select.options, function (opt) {
                if (opt.value === '') return;
                var show = isVisible(opt);
                // hidden saja tidak cukup di sebagian browser, jadi ikut di-disable.
                opt.hidden = !show;
                opt.disabled = !show;
                if (!show && opt.selected) select.value = '';
            });
        }

        function apply() {
            var catValue = category.value;
            var catOption = category.options[category.selectedIndex];
            var isGraded = !!catOption && catOption.dataset.graded === '1';

            // Varietas: hanya milik kategori yang dipilih.
            toggleOptions(variety, function (opt) {
                return !catValue || opt.dataset.parent === catValue;
            });

            // Kelas: hanya tampil untuk Ayam KUB, dan hanya milik varietas yang dipilih.
            if (grade) {
                if (gradeWrap) gradeWrap.style.display = isGraded ? '' : 'none';
                grade.disabled = !isGraded;
                if (!isGraded) grade.value = '';

                toggleOptions(grade, function (opt) {
                    return isGraded && (!variety.value || opt.dataset.parent === variety.value);
                });
            }
        }

        category.addEventListener('change', apply);
        variety.addEventListener('change', apply);
        apply();
    })();
</script>