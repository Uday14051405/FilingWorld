<template>
  <section ref="categorySection">
    <center>
      <div class="row row-cols-4 g-4 justify-content-center card-row " v-if="categoryDetails.length > 0">
        <div v-for="subcategory in categoryDetails.slice(0, 8)" :key="subcategory.id" class="col card-row1">
          <sub-category-card 
            :subcategory_id="subcategory.id" 
            :title="subcategory.name" 
            :description="subcategory.description" 
            :image="subcategory.category_image"
          />
        </div>
      </div>
    </center>

    <center>
      <div class="row row-cols-4 justify-content-center mt-5" v-if="categoryDetails.length == 0">
        <span v-if="isLoading == 0"> Data Not Available </span>
        <SubCategoryShimmer v-if="isLoading == 1" v-for="item in 8" :key="item"></SubCategoryShimmer>
      </div>
    </center>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { SUBCATEGORY_API } from '../data/api'; 
import SubCategoryCard from '../components/SubCategoryCard.vue';
import SubCategoryShimmer from '../shimmer/SubCategoryShimmer.vue';
import { useSection } from '../store/index';

const store = useSection();
const categoryDetails = ref([]);
const categories = ref([]);
const isLoading = ref(1);

// Get all categories
const fetchTopCategories = async () => {
  try {
    const response = await fetch(SUBCATEGORY_API({ per_page: 'all', status: 1 }));
    const data = await response.json();
    if (data && Array.isArray(data.data)) {
      const TotalServices = data.data.filter(user => user.services !== undefined);
      const sortedCategories = TotalServices.sort((a, b) => b.services - a.services);
      categories.value = sortedCategories;
    } else {
      console.error('Invalid data structure or missing array of providers.');
    }
  } catch (error) {
    console.error('Error fetching or processing data:', error);
  }
};

// Get category details
const getCategoryDetails = async () => {
  try {
    await store.get_landing_page_setting_list({ per_page: 'all', page: 1 });
    const settings = store.landing_page_setting_list_data.data.find(setting => setting.key === 'section_18' && setting.status === 1);
    
    if (settings) {
      const categoryIds = getJsonValue(settings.value, 'subcategory_id');
      await fetchTopCategories();
      
      const allCategories = categories.value;
      const selectedCategories = allCategories.filter(category => categoryIds.includes(String(category.id)));

      categories.value = selectedCategories
        .map(category => ({
          id: category.id,
          name: category.name,
          description: category.description,
          category_image: category.category_image,
          order_by: category.order_by || 0,
        }))
        .sort((a, b) => a.order_by - b.order_by);

      categoryDetails.value = categories.value.slice(0, 8);
      isLoading.value = 0;
      
    }
  } catch (error) {
    console.error('Error fetching category details:', error);
  }
};


onMounted(async () => {
  await fetchTopCategories();
  await getCategoryDetails();
});

function getJsonValue(jsonString, key) {
  try {
    const parsedJson = JSON.parse(jsonString);
    return parsedJson[key];
  } catch (error) {
    console.error('Error parsing JSON:', error);
    return null;
  }
}
</script>

<style scoped>
/* Adjust styles for uniform grid */
.row-cols-4 .col {
  display: flex;
  justify-content: center;
  align-items: center;
}

@media (min-width: 320px) and (max-width: 576px){
  .card-row1{
    width: 100% !important;
  }
}

.card-row {
  width: 100%;
  padding-bottom: 25px;
}

@media screen and (max-width: 1024px) {
  .card-row {
      width: 100%;
  }
}
@media screen and (max-width: 768px) {
  .card-row {
      width: 100%;
  }
}
@media screen and (max-width: 576px) {
  .row-cols-4 {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 columns for smaller screens */
    gap: 16px;
  }
}
</style>
