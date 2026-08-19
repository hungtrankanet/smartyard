<?php

/**
 * PostRoutes.php: Static Action POST Route Mappings
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$postRoutesArray = [
    // Admin Actions
    'Admin/deleteNavigationPost', 'Admin/deletePagePost', 'Admin/adSpacesPost', 'Admin/googleAdsenseCodePost',
    'Admin/cacheSystemPost', 'Admin/approveCommentPost', 'Admin/deleteCommentPost', 'Admin/deleteContactMessagePost',
    'Admin/googleNewsPost', 'Admin/seoToolsPost', 'Admin/googleIndexingApiPost', 'Admin/sitemapSettingsPost',
    'Admin/sitemapPost', 'Admin/socialLoginSettingsPost', 'Admin/storagePost', 'Admin/awsS3Post',
    'Admin/setThemePost', 'Admin/setThemeSettingsPost', 'Admin/editFontPost', 'Admin/setSiteFontPost',
    'Admin/addFontPost', 'Admin/deleteFontPost', 'Admin/setActiveLanguagePost', 'Admin/downloadDatabaseBackup',
    'Admin/editMenuLinkPost', 'Admin/addMenuLinkPost', 'Admin/menuLimitPost', 'Admin/sortMenuItems',
    'Admin/hideShowHomeLink', 'Admin/deleteSubscriberPost', 'Admin/newsletterSettingsPost', 'Admin/newsletterSendEmailPost',
    'Admin/addPagePost', 'Admin/editPagePost', 'Admin/deletePagePost', 'Admin/addPollPost',
    'Admin/editPollPost', 'Admin/deletePollPost', 'Admin/emailSettingsPost', 'Admin/emailVerificationSettingsPost',
    'Admin/contactEmailSettingsPost', 'Admin/sendTestEmailPost', 'Admin/generalSettingsPost', 'Admin/recaptchaSettingsPost',
    'Admin/maintenanceModePost', 'Admin/preferencesPost', 'Admin/aiWriterPost', 'Admin/fileUploadSettingsPost',
    'Admin/routeSettingsPost', 'Admin/addUserPost', 'Admin/userOptionsPost', 'Admin/deleteUserPost',
    'Admin/addRolePost', 'Admin/editRolePost', 'Admin/editUserPost', 'Admin/deleteRolePost',
    'Admin/loadUsersDropdown', 'Admin/changeUserRolePost', 'Admin/addWidgetPost', 'Admin/editWidgetPost',
    'Admin/deleteWidgetPost', 'Admin/getMenuLinksByLang', 'Admin/approveSelectedComments', 'Admin/deleteSelectedComments',
    'Admin/deleteSelectedContactMessages',
    // Ajax Actions
    'Ajax/setThemeModePost', 'Ajax/incrementPostViews', 'Ajax/runOnPageLoad', 'Ajax/addPollVote',
    'Ajax/loadMorePosts', 'Ajax/loadMoreUsers', 'Ajax/loadMoreSubscribers', 'Ajax/addRemoveReadingListItem',
    'Ajax/addReaction', 'Ajax/addCommentPost', 'Ajax/loadSubcommentBox', 'Ajax/likeCommentPost',
    'Ajax/loadMoreComments', 'Ajax/deleteCommentPost', 'Ajax/getQuizAnswers', 'Ajax/getQuizResults',
    'Ajax/addPostPollVote', 'Ajax/generateTextAI', 'Ajax/getTagSuggestions',
    // Auth & Category & Earnings & File Actions
    'Auth/loginPost', 'Category/deleteCategoryPost', 'Category/addCategoryPost', 'Category/editCategoryPost',
    'Category/getParentCategoriesByLang', 'Category/getSubCategories', 'Category/addTagPost', 'Category/editTagPost',
    'Category/deleteTagPost', 'Earnings/setPayoutAccountPost', 'Earnings/newPayoutRequestPost',
    'File/uploadFile', 'File/uploadAudio', 'File/uploadImage', 'File/uploadQuizImageFile', 'File/uploadVideo',
    'File/getImages', 'File/deleteImage', 'File/loadMoreImages', 'File/searchImage', 'File/getQuizImages',
    'File/deleteQuizImage', 'File/loadMoreQuizImages', 'File/searchQuizImage', 'File/uploadRecipeImage',
    'File/getRecipeImages', 'File/deleteRecipeImage', 'File/loadMoreRecipeImages', 'File/searchRecipeImage',
    'File/deleteFile', 'File/getFiles', 'File/loadMoreFiles', 'File/searchFiles', 'File/deleteVideo',
    'File/getVideos', 'File/loadMoreVideos', 'File/searchVideos', 'File/deleteAudio', 'File/getAudios',
    'File/loadMoreAudios', 'File/searchAudios',
    // Gallery & Language Actions
    'Gallery/addImagePost', 'Gallery/addAlbumPost', 'Gallery/deleteAlbumPost', 'Gallery/addCategoryPost',
    'Gallery/deleteCategoryPost', 'Gallery/editAlbumPost', 'Gallery/editCategoryPost', 'Gallery/editImagePost',
    'Gallery/deleteImagePost', 'Gallery/setAsAlbumCover', 'Gallery/getAlbumsByLang', 'Gallery/getCategoriesByAlbum',
    'Language/addLanguagePost', 'Language/editLanguagePost', 'Language/setDefaultLanguagePost',
    'Language/exportLanguagePost', 'Language/deleteLanguagePost', 'Language/importLanguagePost', 'Language/editTranslationsPost',
    // Post Actions
    'Post/addPostPost', 'Post/downloadCSVFilePost', 'Post/generateCSVObjectPost', 'Post/importCSVItemPost',
    'Post/postOptionsPost', 'Post/deletePost', 'Post/editPostPost', 'Post/deletePostMainImage',
    'Post/deletePostAdditionalImage', 'Post/setHomeSliderPostOrderPost', 'Post/setFeaturedPostOrderPost',
    'Post/deleteSelectedPosts', 'Post/postBulkOptionsPost', 'Post/getVideoFromURL', 'Post/deletePostVideo',
    'Post/deletePostAudio', 'Post/deletePostFile', 'Post/getListItemHTML', 'Post/addListItem',
    'Post/deletePostListItemPost', 'Post/getQuizQuestionHTML', 'Post/addQuizQuestion', 'Post/getQuizAnswerHTML',
    'Post/addQuizQuestionAnswer', 'Post/deleteQuizQuestion', 'Post/deleteQuizQuestionAnswer',
    'Post/getQuizResultHTML', 'Post/addQuizResult', 'Post/deleteQuizResult', 'Post/testGoogleIndexingApiPost',
    // Reward & RSS & Member & Industry
    'Reward/addPayoutPost', 'Reward/deletePayoutPost', 'Reward/updateSettingsPost', 'Reward/updatePayoutPost',
    'Reward/updateCurrencyPost', 'Reward/approvePayoutPost', 'Rss/editFeedPost', 'Rss/checkFeedPosts',
    'Rss/deleteFeedPost', 'Rss/importFeedPost', 'Member/addMemberPost', 'Member/editMemberPost',
    'Member/deleteMemberPost', 'Member/uploadCardAjax', 'Member/confirmOcrPost', 'Member/verifyMemberAjax',
    'IndustryType/addIndustryPost', 'IndustryType/editIndustryPost', 'IndustryType/deleteIndustryPost',
];

foreach ($postRoutesArray as $item) {
    $array = explode('/', $item);
    $routes->post($item, $array[0] . 'Controller::' . $array[1]);
}
