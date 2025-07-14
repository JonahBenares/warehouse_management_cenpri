<script setup>
	import { onMounted, ref } from "vue"
	import { useRouter } from "vue-router" 
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, Bars3Icon, PlusIcon, MinusCircleIcon, FunnelIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'

	const router = useRouter() 
	let items = ref([])
	let pr = ref([])
	let pritems = ref([])
	let form=ref([]);
	let reports=ref([]);

	onMounted(async () => {
		getItems()
		getPR()
	})

	const getItems = async () => {
		let response = await axios.get("/api/item_variant_list");
		items.value=response.data.items
	}

	const getPR = async () => {
		let response = await axios.get("/api/pr_list");
		pr.value=response.data.pr
	}

	const filter = () => {
		const formDetails = new FormData()
		formDetails.append('item', form.value.item_name)
		formDetails.append('pr', form.value.pr_no)

		axios.post(`/api/filter_pr_variants`, formDetails).then(function (response) {
			//alert(response.data)
			
			reports.value = response.data;
			document.getElementById("showExport").style.display="block"
			
		});

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
							<a onclick="history.back()" class="btn btn-secondary btn-xs btn-rounded text-white">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a>
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">PR w/ Variants Report</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Reports</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
						<div class="table-responsive-md">
							<div class="flex justify-start pb-2 my-2 space-x-2">
								<div class="w-64">
									<v-select v-model="form.item_name" :options="items" :reduce="items => items.id" class="form-control border" :get-option-label="option => `${option.item_description}`" placeholder="Select Item">
										<template #selected-option="{ item_description }">
											{{ item_description }}
										</template>
										<template #option="{ item_description }">
											{{ item_description }}
										</template>
									</v-select>
								</div>
                                <div class="w-64">
									<v-select v-model="form.pr_no" :options="pr" :reduce="pr => pr.pr_no" class="form-control border" :get-option-label="option => `${option.pr_no}`" placeholder="Select PR">
										<template #selected-option="{ pr_no }">
											{{ pr_no }}
										</template>
										<template #option="{ pr_no }">
											{{ pr_no }}
										</template>
									</v-select>
								</div>
								<button class="btn btn-sm btn-success" @click="filter()">
									<div class="flex justify-between space-x-2" >
										<FunnelIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></FunnelIcon>
										<span>Filter</span>
									</div>
								</button>
							</div>
							<hr class="border-dashed">
                            <div class="row" style="display: none;" id="showExport">
                                <div class="col-lg-12 px-1">
                                    <div class="flex justify-end mb-3">
                                        <a :href="'/export-prvariants/'+((form.item_name!='') ? form.item_name : '0')+'/'+((form.pr_no!='') ? form.pr_no : 'null')" class="btn btn-sm btn-success">
                                            <div class="flex justify-between space-x-2" >
                                                <span>Export to Excel</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
							<div>
								<table class="table border mb-0">
									<thead>
										<tr>
											<th class="border font-xxs align-bottom" width="5%">PR</th>
											<th class="border font-xxs align-bottom" width="15%">Item</th>
											<th class="border font-xxs align-top text-center">Variant</th>
											<th class="border font-xxs align-top text-center !bg-yellow-100">Balance</th>
										</tr>
									</thead>
									<tbody v-if="reports.length > 0">
										<tr v-for="r in reports" class="bg-purple-300 shadow">
											<td class="text-xs border font-bold">{{ r.pr_no }}</td>
											<td class="text-xs border font-bold">{{ r.item }}</td>
											<td class="text-xs border font-bold"><span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.supplier_name != ''">{{ r.variant.supplier_name }}</span>
                                                <span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.uom != ''">{{ r.variant.uom }}</span>
                                                <span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.brand != ''">{{ r.variant.brand }}</span>
                                                <span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.color != ''">{{ r.variant.color }}</span>
                                                <span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.size != ''">{{ r.variant.size }}</span>
												<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.item_status != ''">{{ r.item_status }}</span>
												<span class="text-white btn btn-xs py-0 !text-xs bg-gray-400 rounded mr-1" v-if="r.variant.average_cost != '' && r.variant!='Beginning Balance'">{{ r.variant.average_cost+" "+r.variant.currency }}</span></td>
											<td class="text-xs border font-bold text-center font-bold !bg-yellow-100">{{ r.balance }}</td>
										</tr>
									</tbody>
									<tbody v-else>
										<tr><td colspan="14" class="text-xs border font-bold text-center">No data available.</td></tr>
									</tbody>
								</table>
							</div>
							<div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<!-- <TailwindPagination
										:data="departments"
										:limit="1"
										@pagination-change-page="getDepartment"
									/> -->
								</nav>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
