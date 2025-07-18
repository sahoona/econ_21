import { generateClientSideTOC } from './modules/toc.js';
import { initDarkMode } from './modules/darkMode.js';
import { setupFloatingButtons } from './modules/floatingButtons.js';
import { setupSidebar } from './modules/sidebar.js';
import { setupURLCopy } from './modules/copyUrl.js';
import { setupProgressBar } from './modules/progressBar.js';
import { setupStarRating } from './modules/starRating.js';
import { setupReactionButtons } from './modules/reactions.js';
import { setupInfiniteScroll, setupSeriesLoadMoreButton } from './modules/infiniteScroll.js';
// version 1.2
import { setupLazyLoading } from './modules/lazyLoad.js';
import { initAllAds } from '../../components/ads/ads.js';
import { setupPostedDateToggles, setupLanguageToggle, setupCodeCopyButtons, removeProblematicAriaLabel } from './modules/utils.js';

'use strict';

const $ = window.jQuery;

initDarkMode();
document.addEventListener('DOMContentLoaded', setupFloatingButtons);
setupSidebar();
setupURLCopy();
setupProgressBar();
setupStarRating($);
setupReactionButtons($);
setupPostedDateToggles();
setupLanguageToggle();
setupCodeCopyButtons();
setupLazyLoading();
setupInfiniteScroll($);
removeProblematicAriaLabel();
setupSeriesLoadMoreButton($);

if (document.getElementById('gp-toc-container')) {
    generateClientSideTOC();
}

// Initialize ads after the window and all its resources have finished loading.
window.onload = function() {
    initAllAds();
};
