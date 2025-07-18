<template>
  <section ref="blogSection">
    <!-- Search Bar -->
    <div class="row">
      <div class="col-md-12">
        <div class="float-end">
          <div class="search-form input-group flex-nowrap align-items-center">
            <input
              type="search"
              class="form-control rounded-3"
              name="search"
              v-model="search"
              placeholder="Search..."
            />
            <span
              v-if="search"
              class="input-group-text search-icon position-absolute text-body"
              @click="clearSearch"
              style="cursor: pointer"
            >
              <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <line x1="6" y1="18" x2="18" y2="6"
                  stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
                <line x1="6" y1="6" x2="18" y2="18"
                  stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </span>
            <span v-else class="input-group-text search-icon position-absolute text-body">
              <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <circle cx="11.7669" cy="11.7666" r="8.98856"
                  stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
                <path d="M18.0186 18.4851L21.5426 22"
                  stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Blog Cards -->
    <div class="row gy-4 py-4" ref="tableContainer"></div>

    <!-- Bottom Controls: PerPage Selector + Pagination -->
    <div class="row align-items-center mt-4">
      <div class="col-md-6">
        <div class="d-flex align-items-center gap-2">
          <label for="perPage" class="form-label mb-0">Show</label>
          <select
            id="perPage"
            class="form-select w-auto"
            v-model="perPage"
            @change="loadBlogs(1)"
          >
            <option v-for="option in perPageOptions" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
          <span>entries</span>
        </div>
      </div>
      <div class="col-md-6 d-flex justify-content-end">
        <ul class="pagination mb-0" ref="paginationContainer"></ul>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps(['link']);

const search = ref('');
const perPage = ref(12);
const perPageOptions = [12, 25, 50, 100];

const tableContainer = ref(null);
const paginationContainer = ref(null);

const loadBlogs = (page = 1) => {
  window.$.ajax({
    url: props.link,
    data: {
      search: search.value,
      length: perPage.value,
      start: (page - 1) * perPage.value,
      draw: 1,
    },
    success: (response) => {
      const blogs = response.data || [];
      const total = response.recordsTotal;

      // Clear and render blog cards
      tableContainer.value.innerHTML = '';
      blogs.forEach((item) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'col-lg-3 col-md-6';
        wrapper.innerHTML = item.name;
        tableContainer.value.appendChild(wrapper);
      });

      // Build pagination
      const pages = Math.ceil(total / perPage.value);
      paginationContainer.value.innerHTML = '';
      for (let i = 1; i <= pages; i++) {
        const li = document.createElement('li');
        li.className = 'page-item' + (i === page ? ' active' : '');
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.innerText = i;
        a.onclick = (e) => {
          e.preventDefault();
          loadBlogs(i);
        };
        li.appendChild(a);
        paginationContainer.value.appendChild(li);
      }
    },
  });
};

watch(search, () => loadBlogs(1));

onMounted(() => {
  loadBlogs(1);
});

const clearSearch = () => {
  search.value = '';
};
</script>
