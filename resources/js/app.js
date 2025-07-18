import * as $ from 'jquery'
window.jQuery = window.$ = $
import 'select2'
// import 'swiper/scss';
// import 'swiper/scss/navigation';
// import 'swiper/scss/pagination';
import 'swiper/swiper-bundle.min.css'
import './handyman'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import 'select2/dist/css/select2.min.css'
import ServiceSliderSection from './sections/ServiceSliderSection.vue'
import ProductSliderSection from './sections/ProductSliderSection.vue'
import CategorySection from './sections/CategorySection.vue'
import SubCategorySection from './sections/SubCategorySection.vue'
import CategoryPage from './sections/CategoryPage.vue'
import SubCategoryPage from './sections/SubCategoryPage.vue'
import ProductCategoryPage from './sections/ProductCategoryPage.vue'
import ProductSubCategoryPage from './sections/ProductSubCategoryPage.vue'
import ServicePage from './sections/ServicesPage.vue'
import ProductPage from './sections/ProductsPage.vue'
import BlogPage from './sections/BlogPage.vue'
import BlogPageIndex from './sections/BlogPageIndex.vue'
import ProviderPage from './sections/ProviderPage.vue'
import BookingPage from './sections/BookingPage.vue'
import PostJobPage from './sections/PostJobPage.vue'
import TestimonialSection from './sections/TestimonialSection.vue'
import ServiceListSection from './sections/ServiceListSection.vue'
import ProductListSection from './sections/ProductListSection.vue'
import BlogSlidersection from './sections/BlogSliderSection.vue'
import ServicePackageSection from './sections/ServicePackageSection.vue'
import ServicePackagePage from './sections/ServicePackagePage.vue'
import ProductPackagePage from './sections/ProductPackagePage.vue'
import ServicedetailSection from './sections/ServicedetailSection.vue'
import RelatedServicePage from './sections/RelatedServicePage.vue'

import LocationSearch from './components/LocationSearch.vue'
import TopProvider from './components/TopProvider.vue'

import SectionThumbnailSection from './sections/SectionThumbnailSection.vue'
import PaginationCard from './components/PaginationCard.vue'
import RatingCard from './components/RatingCard.vue'
import BookingWizard from './sections/BookingWizard.vue'
import ProductBookingWizard from './sections/ProductBookingWizard.vue'
import RatingAllPage from './sections/RatingAllPage.vue'
import BookingRating from './sections/BookingRating.vue'
import HandymanRating from './sections/HandymanRating.vue'
import Payment from './components/Payment.vue'
import PostJobForm from './sections/PostJobForm.vue'
import BookingPostJob from './sections/BookingPostJob.vue'
import Wallet from './components/Wallet.vue'
import HelpdeskPage from './sections/HelpdeskPage.vue'
import HelpdeskTable from './sections/HelpdeskTable.vue'
import axios from 'axios';

const pinia = createPinia()

const app = createApp()

app.use(pinia)

app.component('service-slider-section', ServiceSliderSection)
app.component('product-slider-section', ProductSliderSection)
app.component('category-section', CategorySection)
app.component('sub-category-section', SubCategorySection)
app.component('category-page', CategoryPage)
app.component('subcategory-page', SubCategoryPage)
app.component('product-category-page', ProductCategoryPage)
app.component('product-subcategory-page', ProductSubCategoryPage)
app.component('service-page', ServicePage)
app.component('product-page', ProductPage)
app.component('blog-page', BlogPage)
app.component('blog-page-index', BlogPageIndex)
app.component('provider-page', ProviderPage)
app.component('booking-page', BookingPage)
app.component('helpdesk-page', HelpdeskPage)
app.component('helpdesk-table', HelpdeskTable)
app.component('post-job-page', PostJobPage)
app.component('testimonial-section', TestimonialSection)
app.component('service-list-section', ServiceListSection)
app.component('product-list-section', ProductListSection)
app.component('blog-slider-section', BlogSlidersection)
app.component('service-package-section', ServicePackageSection)
app.component('service-package-page', ServicePackagePage)
app.component('product-package-page', ProductPackagePage)
app.component('landing-servicedetailsection-section', ServicedetailSection)
app.component('related-service-page', RelatedServicePage)
// app.component('landing-sectiondetailthumbnail-section', SectiondetailThumbnail)
// app.component('global-pagination', GlobalPagination)
app.component('location-search', LocationSearch)
app.component('top-provider', TopProvider)
app.component('section-thumbnail-section', SectionThumbnailSection)
app.component('pagination-component', PaginationCard)
app.component('rating-component', RatingCard)
app.component('booking-wizard', BookingWizard)
app.component('product-booking-wizard', ProductBookingWizard)
app.component('rating-all-page', RatingAllPage)
app.component('booking-rating', BookingRating)
app.component('handyman-rating', HandymanRating)
app.component('payment', Payment)
app.component('post-job-form', PostJobForm)
app.component('booking-post-job', BookingPostJob)
app.component('wallet', Wallet)

/*--------------------------------------
Calculate Header height
------------------------------------------*/
function headerHeightCount() {
  let is_header = document.querySelector('header .iq-navbar')
  if (is_header !== null) {
    let headerHeight = document.querySelector('header .iq-navbar')?.getBoundingClientRect().height
    document.querySelector(':root').style.setProperty('--header-height', headerHeight + 'px')
  }
}

headerHeightCount()

//   jQuery(window).on('resize', function () {
//     headerHeightCount();
//   });

export const confirmcancleSwal = async ({ title }) => {
  return await Swal.fire({
    title: title,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#858482',
    confirmButtonText: 'Yes, do it!'
  }).then((result) => {
    return result
  })
}

function formatCurrency(number, noOfDecimal, currencyPosition, currencySymbol) {
  let formattedNumber = number.toFixed(noOfDecimal)

  let [integerPart, decimalPart] = formattedNumber.split('.')

  integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',')

  decimalPart = decimalPart || ''

  let currencyString = ''

  if (currencyPosition === 'left') {
    currencyString += currencySymbol

    currencyString += integerPart

    if (noOfDecimal > 0) {
      currencyString += '.' + decimalPart
    }
  }

  if (currencyPosition === 'right') {
    // Add decimal part and decimal separator if applicable
    if (noOfDecimal > 0) {
      currencyString += integerPart + '.' + decimalPart
    }

    currencyString += currencySymbol
  }

  return currencyString
}

window.formatCurrency = formatCurrency
const i18n = createI18n({
  legacy: false,
  locale: 'en',
  globalInjection: true,
  messages: { en: window.localMessagesUpdate } || {}
})

window.i18n = i18n

app.use(i18n)
app.mount('#landing-app')


// Function to update user activity
function updateUserActivity() {
  axios.post('/api/update-last-activity', {}, {
      headers: {
          Authorization: `Bearer ${localStorage.getItem("auth_token")}`
      }
  }).then(() => {
      console.log("User activity updated.");
  }).catch(error => {
      console.error("Error updating activity:", error);
  });
}

// Call the API every 1 minute (60,000 milliseconds)
setInterval(updateUserActivity, 60000);

window.addEventListener("beforeunload", function () {
  axios.post('/api/update-last-activity', {}, {
      headers: {
          Authorization: `Bearer ${localStorage.getItem("auth_token")}`
      }
  }).catch(error => console.error("Error tracking logout:", error));
});