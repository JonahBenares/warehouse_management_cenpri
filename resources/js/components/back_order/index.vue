<script setup>
import navigation from '@/layouts/navigation.vue';
import { PencilSquareIcon, Bars3Icon, PlusIcon, MagnifyingGlassIcon, ArrowUturnLeftIcon, EyeIcon } from '@heroicons/vue/24/solid'
import axios from "axios";
import {onMounted, ref} from "vue";
import { useRouter } from "vue-router";
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net';
onMounted(() => {
	$('#main_table').DataTable();
});
import $ from 'jquery'
const router = useRouter()
let searchBackorder=ref([]);
let backorder=ref([]);

		onMounted(async () => {
			getBackorder()
		})

		const getBackorder = async (page = 1) => {
			let response = await axios.get(`/api/get_all_backorder`)
			backorder.value = response.data
			// const response = await fetch(`/api/get_all_backorder?page=${page}&filter=${searchBackorder.value}`);
			// backorder.value = await response.json();
		}

		const showTransaction = (id) => {
		router.push('/back_order/show/'+id)
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
							<div>
								<h6 class="m-0 pt-1 font-bold uppercase">Back Order</h6>
							</div>
						</div>	
						<div class="pt-1">	
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb adminx-page-breadcrumb">
									<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Back Order</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>	

				<div class="row">
					<div class="col-md-12 col-lg-12 ">
						<div class="card">
							<div class="table-responsive-md">
								<div class="flex justify-between pb-2 mt-2 mb-2">
									<div class="flex justify-between">
										<div class="input-group border rounded-xl w-80">
											<div class="input-group-prepend">
												<div class="input-group-icon pt-1 pl-1">
													<MagnifyingGlassIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></MagnifyingGlassIcon>
												</div>
											</div>
											<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getBackorder()" v-model="searchBackorder">
										</div>
									</div>
									<a href="/back_order/new/0" class="btn btn-sm btn-primary btn-rounded">
										<div class="flex justify-between space-x-2" >
											<PlusIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></PlusIcon>
											<span>Add New</span>
										</div>
									</a>
								</div>
								<table id="main_table" class="table table-actions table-hover mb-0">
									<thead>
										<tr>
											<th scope="col" width="20%">MRIF No.</th>
											<th scope="col" width="14%">Backorder Date</th>
											<th scope="col" width="20%">DR No.</th>
											<th scope="col" width="20%">PO No.</th>
											<th scope="col" width="20%">PR No/s</th>
											<th scope="col" width="20%">SI No.</th>
											<th scope="col" width="1%" class="p-1">
												<div class="flex justify-center">
													<Bars3Icon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></Bars3Icon>
												</div>
											</th>
										</tr>
									</thead>
									<tbody v-for="bo in backorder">
										<tr>
											<td>{{ bo.mrecf_no }}</td>
											<td>{{ bo.backorder_date }}</td>
											<td>{{ bo.dr_no }}</td>
											<td>{{ bo.po_no }}</td>
											<td>{{ bo.pr_no }}</td>
											<td>{{ bo.si_or }}</td>
											<td class="p-1 ">
												<div class="flex justify-center">
													<a @click="showTransaction(bo.id)" class="btn btn-xs btn-warning text-white btn-rounded">
														<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
													</a>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="flex justify-end p-2 border-t">
									<nav aria-label="Page navigation example">
										<TailwindPagination
											:data="backorder"
											:limit="1"
											@pagination-change-page="getBackorder"
										/>
									</nav>
								</div>
							</div>
						</div>
					</div>

				</div>


			</div>
    </navigation>
</template>
