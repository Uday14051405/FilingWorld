<template>
 
     <div class="row align-items-center">
          <div class="col-md-9 col-sm-8">
               <h4 class="text-capitalize mb-0">{{$t('messages.all_reviews')}} ({{ review_count }})</h4>
          </div>
          <div class="col-md-3 col-sm-4 mt-sm-0 mt-3">
               <div class="search-form input-group flex-nowrap align-items-center">
                    <input type="search" class="form-control rounded-3" name="search" v-model="search" placeholder="Search...">
                    <span class="input-group-text search-icon position-absolute text-body">
                         <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                              </circle>
                              <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                              </path>
                         </svg>
                    </span>
               </div>
          </div>
     </div>
     <div class="table-responsive rounded py-4">
          <table id="datatable" ref="tableRef" class="table custom-card-table"></table>
     </div>
 
 </template>
 <script setup>
 import { computed, ref, watch} from 'vue';
 import {useSection} from '../store/index'
 import {useObserveSection} from '../hooks/Observer'
 import useDataTable from '../hooks/Datatable'
 
 const props = defineProps(['link','review_count']);

 console.log('Link from props:', props.link);

 
 const search = ref('')
 watch(() => search.value, () => ajaxReload())
 
 const ajaxReload = () => window.$(tableRef.value).DataTable().ajax.reload(null, false)
 
 const columns = ref([
   { data: 'name', title: '', orderable: false, }
 ]);
 
 const tableRef = ref(null);
 
 useDataTable({
   tableRef: tableRef,
   columns: columns.value,
   url: props.link,
   dom: '<"row align-items-center"><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" l><"col-md-6" p>><"clear">',
   advanceFilter: () => {
     return {
         search: search.value,
     }
   }
 });
 
 const store = useSection()
 
 </script>
 