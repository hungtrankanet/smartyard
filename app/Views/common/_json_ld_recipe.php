<?php $totalTime = !empty($dataRecipe) && !empty($dataRecipe['prep_time']) ? $dataRecipe['prep_time'] : 0;
$totalTime += !empty($dataRecipe) && !empty($dataRecipe['cook_time']) ? $dataRecipe['cook_time'] : 0;
$recipeCategory = getCategory($post->category_id, $baseCategories); ?>
<script type="application/ld+json">
{
"@context": "https://schema.org/",
"@type": "Recipe",
"name": "<?= escMeta($postJsonLD->title); ?>",
"image": {"@type": "ImageObject","url": "<?= getPostImage($postJsonLD, 'big'); ?>","width": 750,"height": 500},
"author": {"@type": "Person","name": "<?= escMeta($postJsonLD->author_username); ?>", "url": "<?= base_url("profile/".$postJsonLD->author_slug); ?>"},
"datePublished": "<?= date(DATE_ISO8601, strtotime($postJsonLD->created_at)); ?>",
"description": "<?= escMeta($postJsonLD->summary); ?>",
"prepTime": "PT<?= !empty($dataRecipe) && !empty($dataRecipe['prep_time']) ? $dataRecipe['prep_time'] : ''; ?>M",
"cookTime": "PT<?= !empty($dataRecipe) && !empty($dataRecipe['cook_time']) ? $dataRecipe['cook_time'] : ''; ?>M",
"totalTime": "PT<?= $totalTime; ?>M",
"keywords": "<?= escMeta($keywords); ?>",
"recipeYield": "<?= !empty($dataRecipe) && !empty($dataRecipe['serving']) ? $dataRecipe['serving'] : 1; ?> servings",
"recipeCategory": "<?= !empty($recipeCategory) ? escMeta($recipeCategory->name) : ''; ?>",
<?php if (!empty($dataRecipe['nInfo']) && is_array($dataRecipe['nInfo'])): ?>
"nutrition": {
"@type": "NutritionInformation",
<?php
$nutritionArray = [];
foreach ($dataRecipe['nInfo'] as $itemInfo) {
if (!empty($itemInfo['n']) && !empty($itemInfo['v'])) {
$nutritionArray[] = '"' . escMeta($itemInfo['n']) . '": "' . escMeta($itemInfo['v']) . '"';
}}
    echo implode(',', $nutritionArray);
?>

},<?php endif; ?>

<?php if(!empty($dataRecipe['ingredients']) && countItems($dataRecipe['ingredients']) > 0) {
echo trim(substr(json_encode(["recipeIngredient" => $dataRecipe['ingredients']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), 1, -1)) . ',';
}?>

<?php if(!empty($recipeInstructions)){
echo trim(substr(json_encode(["recipeInstructions" => $recipeInstructions], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), 1, -1)) . ',';
} ?>

<?php if(!empty($post->video_embed_code)):?>
"video": {
"@type": "VideoObject",
"name": "<?= escMeta($postJsonLD->title); ?>",
"description": "<?= escMeta($postJsonLD->summary); ?>",
"thumbnailUrl": "<?= getPostImage($postJsonLD, 'big'); ?>",
"contentUrl": "<?= escMeta($postJsonLD->video_url); ?>",
"embedUrl": "<?= escMeta($postJsonLD->video_embed_code); ?>",
"uploadDate": "<?= date(DATE_ISO8601, strtotime($postJsonLD->created_at)); ?>"}
<?php endif; ?>
}
</script>