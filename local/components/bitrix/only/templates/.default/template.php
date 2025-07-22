<?php foreach ($arResult['ITEMS'] as $iblockId => $items): ?>
    <h3>Инфоблок <?= $iblockId ?></h3>
    <ul>
        <?php foreach ($items as $item): ?>
            <li><?= $item['NAME']?></li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>