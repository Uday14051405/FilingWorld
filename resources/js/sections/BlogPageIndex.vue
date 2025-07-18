<template>
    <section ref="blogSection">
        <div class="table-responsive rounded py-4">
            <table id="datatable" ref="tableRef" class="table custom-card-table"></table>
        </div>
    </section>

</template>
<script setup>
import { computed,ref,watch} from 'vue';
import BlogCard from '../components/BlogCard.vue';
import BlogShimmer  from '../shimmer/BlogShimmer.vue'

import {useSection} from '../store/index'
import {useObserveSection} from '../hooks/Observer'
import useDataTable from '../hooks/Datatable'

const props = defineProps(['link']);

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
  paging: false,
  dom: '<"row align-items-center"><"table-responsive my-3" rt><"clear">',
  advanceFilter: () => {
    return {
        search: search.value,
    }
  }
});

const store = useSection()
const blog_data = computed(() => store.blog_list_data)

const [blogSection] = useObserveSection(() => store.get_blog_list({per_page: "all"}))

const clearSearch = () =>{
  search.value = '';
}
</script>
