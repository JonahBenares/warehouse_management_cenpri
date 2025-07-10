<script setup>
	import navigation from '@/layouts/navigation.vue';
	import { EyeIcon ,PencilSquareIcon, TrashIcon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, Bars3Icon, ArrowUturnLeftIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
	import {onMounted, ref} from "vue";
	import { useRouter } from "vue-router";
	import jszip from 'jszip';
	import 'datatables.net-dt/css/dataTables.dataTables.css';
    import 'datatables.net';
    onMounted(() => {
        $('#main_table').DataTable();
    });
    import $ from 'jquery'
	const router = useRouter();
	let items=ref([]);
	let quantity=ref([]);
	let searchItems=ref([]);
	onMounted(async () => {
		getItems()
	})
	const getItems = async (page = 1) => {
		let response = await axios.get(`/api/get_all_items?page=${page}`);
		items.value=response.data.itemsarray
	}
	const onEdit= (id) => {
		router.push('/item_list/edit/'+id)
	}
</script>
<template>
    <navigation>
		<div class="container-fluid">
				<!-- BreadCrumb -->
				<div class="card mb-3">	
					<div class="flex justify-between content-center">
						<div class="flex justify-start space-x-3 ">
							<div class="">
								<a href="/dashboard" class="btn btn-secondary btn-xs btn-rounded">
									<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
								</a>
							</div>
							<!-- <div class="border-r"></div>	 -->
							<div>
								<h6 class="m-0 pt-1 font-bold uppercase">Item List</h6>
							</div>
						</div>	
						<div class="pt-1">	
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb adminx-page-breadcrumb">
									<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Item List</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>	
				<div class="row">
					<div class="col-md-12 col-lg-12 ">
						<div class="card">
							<div class="table-responsive-md pt-3">
								<div class="flex justify-between pb-2 mt-0 mb-2 s z-50 ">
									<a href="/item_list/new" class="btn btn-sm btn-primary btn-rounded pull-left">
										<div class="flex justify-between space-x-2" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
								<table id="main_table" class="display" width="100%">  
									<thead>
										<tr>
											<th scope="col" width="15%" class="text-sm !text-gray-600">Orignal PN</th>
											<th scope="col" width="30%" class="text-sm !text-gray-600">Item Description</th>
											<th scope="col" width="15%" class="text-sm !text-gray-600">Location</th>
											<th scope="col" width="10%" class="text-sm !text-gray-600">Rack</th>
											<th scope="col" width="10%" class="text-sm !text-gray-600 text-center">Qty</th>
											<th scope="col" width="10%" class="text-sm !text-gray-600 text-center">MOQ</th>
											<th scope="col" width="1%" align="center" class="pr-2">
												<div class="flex justify-center">
													<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
												</div>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									</tbody>
									<!-- <template #column-1="props">
										{{ props.rowData.item_description }}
										<button v-if="props.rowData.qty==0 && props.rowData.id==props.rowData.item_id" class="btn btn- !text-yellow-400 btn-rounded p-0 btn-sm ml-1">
											<ExclamationTriangleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></ExclamationTriangleIcon>
										</button>
									</template>
									<template #column-6="props">
										<a  href="#" @click="onEdit(props.rowData.id)" class="btn btn-xs btn-info btn-rounded text-white" v-if="props.rowData.draft==0">
											<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
										</a>
										<a v-else href="#" @click="onEdit(props.rowData.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded" title="Draft">
											<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
										</a>
									</template> -->
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
    </navigation>
</template>
