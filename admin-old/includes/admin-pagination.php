<div class="pagination">

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>

        <a
            href="?page=<?= $p ?>"
            class="page-btn <?= $p == $page ? 'active' : '' ?>">
            <?= $p ?>
        </a>

    <?php endfor; ?>

</div>