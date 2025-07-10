<script setup>
	import{ onMounted, ref } from "vue"	
	import navigation from '@/layouts/navigation.vue';
	import { EyeIcon, XMarkIcon, CheckIcon, MagnifyingGlassIcon, Bars3Icon, ChevronRightIcon, ArrowUturnLeftIcon, BarsArrowUpIcon, ClipboardDocumentIcon } from '@heroicons/vue/24/solid'
	import 'datatables.net-dt/css/dataTables.dataTables.css';
    import 'datatables.net';
    onMounted(() => {
        $('#main_table').DataTable();
    });
    import $ from 'jquery'
	import jszip from 'jszip';
    import moment from 'moment'
	import { useRouter } from "vue-router"
    const router = useRouter()
	let head = ref([])
	let pending_items = ref(0)
	let searchRecieve=ref([]);
	onMounted(async () =>{
        getAllBackorder()
    })

	const getAllBackorder = async (page = 1) => {
		// const response = await fetch(`/api/get_all_useracceptance?page=${page}&filter=${searchRecieve.value}`);
		// head.value = await response.json();
		let response = await axios.get(`/api/get_all_backuseracceptance?page=${page}`);
		head.value=response.data.backorderarray
	}

    const acceptModal = ref(false)
    const rejectModal = ref(false)
	const hideModal = ref(true)
	const openAccept = () => {
		acceptModal.value = !acceptModal.value
	}
    const openReject = () => {
		rejectModal.value = !rejectModal.value
	}
	const closeModal = () => {
		acceptModal.value = !hideModal.value
        rejectModal.value = !hideModal.value
	}

	const showTransaction = (id) => {
		router.push('/user_acceptance/backorder_view/'+id)
	}
</script>

<template>
    <navigation>
		<div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class=" ">
							<a href="/dashboard" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Backorder User Acceptance <span  class="text-sm text-yellow-600">(Pending)</span></h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Backorder User Acceptance (Pending)</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="table-responsive-md">
							<!-- <div class="flex justify-between pb-2 mt-2 mb-2">
								<div class="flex justify-between">
									<div class="input-group border rounded-xl w-80">
										<div class="input-group-prepend">
											<div class="input-group-icon pt-1 pl-1">
												<MagnifyingGlassIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></MagnifyingGlassIcon>
											</div>
										</div>
										<input type="text" class="form-control border-0" id="search" placeholder="Type to search..." @keyup="getAllReceive()" v-model="searchRecieve">
									</div>
								</div>
								<div class="flex justify-between space-x-1">
									<a href="export" class="btn btn-sm btn-success btn-rounded">
										<div class="flex justify-between space-x-1" >
											<BarsArrowUpIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></BarsArrowUpIcon>
											<span>Export</span>
										</div>
									</a>
								</div>
							</div> -->
							<!-- <table class="table table-actions table-bordesred table-hover mb-0"> -->
							<table id="main_table" class="table table-actions table-bordesred table-hover mb-0" width="100%">
								<thead>
									<tr>
										<th scope="col" width="15%">MrecF No.</th>
										<th scope="col" width="14%">Receive Date</th>
										<th scope="col" width="12%">DR No.</th>
										<th scope="col" width="12%">PO No.</th>
										<th scope="col" width="12%">SI No.</th>
										<th scope="col" width="12%">Waybill No.</th>
                                        <th scope="col" width="5%" class="text-center">Pending Items</th>
										<th scope="col" width="20%">PR No/s</th>
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
										<td></td>
										<td></td>
									</tr>
								</tbody>
								<!-- <template #column-6="props">
									<span class="badge badge-pill badge-danger" v-if="props.rowData.pending_items != 0"> {{ props.rowData.pending_items }}</span>
								</template>
								<template #column-7="props">
									<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-for="pr in props.rowData.backorder_details ">{{ pr.pr_no + ' ' }}</span>
								</template>
								<template #column-8="props">
									<a @click="showTransaction(props.rowData.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded">
										<EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
									</a>
								</template> -->
							</table>
								<!-- <tbody>
									<tr v-for="h in head.data">
										<td>{{ h.mrecf_no }}</td>
										<td>{{ h.receive_date }}</td>
										<td>{{ h.dr_no }}</td>
										<td>{{ h.po_no }}</td>
										<td>{{ h.si_or }}</td>
                                        <td align="center">
											<span class="badge badge-pill badge-danger" v-if="pending_items != 0"> {{ pending_items }}</span>
                                            <span class="badge badge-pill badge-success" v-if="h.closed === 0"> Open</span>
                                            <span class="badge badge-pill badge-danger" v-else>Close</span>
                                        </td>
										<td>
											<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-for="pr in h.receive_details ">{{ pr.pr_no + ' ' }}</span>
										</td>
										<td class="pl-2 pr-2 font-bold">
                                            <div class="space-x-1 flex justify-center">
                                                <a  @click="openAccept()" class="text-white btn btn-xs bg-green-500 btn-rounded">
                                                    <CheckIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></CheckIcon>
                                                </a>
                                                <a @click="openReject()" class="text-white btn btn-xs bg-red-500 btn-rounded">
                                                    <XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></XMarkIcon>
                                                </a>
                                                <a @click="showTransaction(h.id)" class="text-white btn btn-xs bg-yellow-500 btn-rounded">
                                                    <EyeIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></EyeIcon>
                                                </a>
                                            </div>
										</td>
									</tr>
									
								</tbody>
							</table> -->
							<!-- <div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
										:data="head"
										:limit="1"
										@pagination-change-page="getAllReceive"
									/>
								</nav>
							</div> -->
						</div>
					</div>
				</div>
			</div>
		</div>
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
			<div class="modal pt-4 px-3" :class="{ show:acceptModal }">
				<div @click="closeModal" class="w-full h-full fixed"></div>
				<div class="modal__content w-4/12 !mt-20">
					<div class="modal_s_items">
						<div class=" ">
							<div class="row">
								<div class="col-lg-12">
                                    <div class="border-b mb-3">
                                        <h6 class=" font-bold uppercase">Accept Receive</h6>
                                    </div>
                                    <div class="">
                                        <div class="form-group w-full">
                                            <label class="form-label mb-0">Date</label>
                                            <input type="date" class="form-control border" >
                                        </div>
                                        <div class="form-group w-full">
                                            <label class="form-label mb-0">Accepted by</label>
                                            <select class="form-control border py-2" >
                                                <option value="">asdas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="pt-2 mb-2 flex justify-end">
                                        <button class="btn btn-sm btn-primary btn-rounded w-32">Submit</button>
                                    </div>
								</div>
							</div>
						</div>		
					</div> 
				</div>
			</div>
		</Transition>
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
			<div class="modal pt-4 px-3" :class="{ show:rejectModal }">
				<div @click="closeModal" class="w-full h-full fixed"></div>
				<div class="modal__content w-6/12 !mt-20">
					<div class="modal_s_items">
						<div class=" ">
							<div class="row">
								<div class="col-lg-12">
                                    <div class="border-b mb-3">
                                        <h6 class=" font-bold uppercase">Reject Receive</h6>
                                    </div>
                                    <div class="flex justify-between space-x-2">
                                        <div class="form-group w-5/12">
                                            <label class="form-label mb-0">Date</label>
                                            <input type="date" class="form-control border" >
                                        </div>
                                        <div class="form-group w-full">
                                            <label class="form-label mb-0">Rejected by</label>
                                            <select class="form-control border py-2" >
                                                <option value="">asdas</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label mb-0">Reason</label>
                                        <textarea type="date" class="form-control border" ></textarea>
                                    </div>
                                    <div class="pt-2 mb-2 flex justify-end">
                                        <button class="btn btn-sm btn-primary btn-rounded w-32">Submit</button>
                                    </div>
								</div>
							</div>
						</div>		
					</div> 
				</div>
			</div>
		</Transition>
    </navigation>
</template>
