<?php $socialArray = getSocialLinksArray($baseSettings);
$i = 0; ?>
<script type="application/ld+json">[{
"@context": "http://schema.org",
"@type": "Organization",
"url": "<?= base_url(); ?>",
"logo": {"@type": "ImageObject","width": 190,"height": 60,"url": "<?= getLogo(); ?>"}<?= !empty($socialArray) ? ',' : ''; ?>
<?php if (!empty($socialArray) && countItems($socialArray)): ?>
"sameAs": [<?php foreach ($socialArray as $item):if (!empty($item['value'])): ?><?= $i != 0 ? ',' : ''; ?>"<?= escMeta($item['value']); ?>"<?php endif;
$i++;endforeach; ?>]
<?php endif; ?>
},
{
    "@context": "http://schema.org",
    "@type": "WebSite",
    "url": "<?= base_url(); ?>",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "<?= base_url(); ?>/search?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}]
</script>
<?php if (!empty($postJsonLD)):
$dateModified = $postJsonLD->updated_at;
if (empty($dateModified)) {
$dateModified = $postJsonLD->created_at;
}
if ($postJsonLD->post_type != 'recipe'): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?= generatePostURL($postJsonLD); ?>"
    },
    "headline": "<?= escMeta($postJsonLD->title); ?>",
    "name": "<?= escMeta($postJsonLD->title); ?>",
    "articleSection": "<?= escMeta($postJsonLD->category_name); ?>",
    "image": {
        "@type": "ImageObject",
        "url": "<?= getPostImage($postJsonLD, 'big'); ?>",
        "width": 750,
        "height": 500
    },
    "datePublished": "<?= date(DATE_ISO8601, strtotime($postJsonLD->created_at)); ?>",
    "dateModified": "<?= date(DATE_ISO8601, strtotime($dateModified)); ?>",
    "inLanguage": "<?= $activeLang->language_code; ?>",
    "keywords": "<?= $postJsonLD->keywords; ?>",
    "author": {
        "@type": "Person",
        "name": "<?= escMeta($postJsonLD->author_username); ?>",
        "url": "<?= base_url("profile/".$postJsonLD->author_slug); ?>"
    },
    "publisher": {
    "@type": "Organization",
    "name": "<?= clrQuotes($baseSettings->application_name); ?>",
    "logo": {
        "@type": "ImageObject",
        "width": 190,
        "height": 60,
        "url": "<?= getLogo(); ?>"
    }
    },
    "description": "<?= escMeta($postJsonLD->summary); ?>"
}
</script>
<?php endif;
endif;
if (!empty($category)):
if (!empty($parentCategory)):?>
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "<?= escMeta($parentCategory->name); ?>",
    "item": "<?= generateCategoryURL($parentCategory); ?>"
    },
    {
        "@type": "ListItem",
        "position": 2,
        "name": "<?= escMeta($category->name); ?>",
        "item": "<?= generateCategoryURL($category); ?>"
    }]
}
</script>
<?php else: ?>
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "<?= escMeta($category->name); ?>",
    "item": "<?= generateCategoryURL($category); ?>"
    }]
}
</script>
<?php endif;
endif; ?>