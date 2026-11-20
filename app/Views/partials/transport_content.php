<?php
$sections = [['title' => null, 'body' => [], 'cards' => []]];
$sectionIndex = 0;
$cardIndex = null;
foreach (preg_split('/\R\s*\R/u', trim($content)) as $block) {
    $block = trim($block);
    if ($block === '') {
        continue;
    }
    if (preg_match('/\A## ([^\r\n]+)\z/u', $block, $heading)) {
        $sections[] = ['title' => $heading[1], 'body' => [], 'cards' => []];
        $sectionIndex = count($sections) - 1;
        $cardIndex = null;
    } elseif (preg_match('/\A### ([^\r\n]+)\z/u', $block, $heading)) {
        $sections[$sectionIndex]['cards'][] = ['title' => $heading[1], 'body' => []];
        $cardIndex = count($sections[$sectionIndex]['cards']) - 1;
    } elseif ($cardIndex !== null) {
        $sections[$sectionIndex]['cards'][$cardIndex]['body'][] = $block;
    } else {
        $sections[$sectionIndex]['body'][] = $block;
    }
}
?>
<?php foreach ($sections as $section): ?>
    <?php if ($section['title'] !== null || $section['body'] !== [] || $section['cards'] !== []): ?>
        <section class="transport-section">
            <?php if ($section['title'] !== null): ?><h2><?= esc($section['title']) ?></h2><?php endif; ?>
            <?php foreach ($section['body'] as $paragraph): ?><p><?= nl2br(esc($paragraph)) ?></p><?php endforeach; ?>
            <?php if ($section['cards'] !== []): ?>
                <div class="content-grid">
                    <?php foreach ($section['cards'] as $card): ?>
                        <article class="content-panel">
                            <h3><?= esc($card['title']) ?></h3>
                            <?php foreach ($card['body'] as $paragraph): ?><p><?= nl2br(esc($paragraph)) ?></p><?php endforeach; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
<?php endforeach; ?>
