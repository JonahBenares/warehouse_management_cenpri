<script setup>
	import { onMounted, ref } from "vue"
	import { useRouter } from "vue-router" 
	import navigation from '@/layouts/navigation.vue';
	import { FunnelIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
	
	import jszip from 'jszip';
	import $ from 'jquery';
    import 'datatables.net-dt/css/dataTables.dataTables.css';
    import 'datatables.net';
    
    onMounted(() => {
        $('#main_table').DataTable();
    });
    import moment from 'moment'
	const router = useRouter() 
	let form = ref({
        item_name:'',
		supplier_name:'',
		department:'',
		catalog:'',
		brand:''
    })
	let items = ref([])
	let department = ref([])
	let supplier = ref([])
	let brand = ref([])
	let catalog = ref([])
	let stockcard=ref([]);
	let balance=ref([]);
	let quantity=ref([]);
	let overall_qty=ref(0);
	let itemname=ref('');
	const props = defineProps({
		variant_id:{
			type:String,
			default:'0'
		},
		item_id:{
			type:String,
			default:'0'
		},
		supplier_id:{
			type:String,
			default:'0'
		},
		catalog_no:{
			type:String,
			default:'null'
		},
		brand:{
			type:String,
			default:'null'
		},
		department_id:{
			type:String,
			default:'0'
		}
	})
	onMounted(async () => {
		getItems()
		getDepartment()
		getSupplier()
		getBrand()
		getCatalog()
		getStockcard()
	})

	const getItems = async () => {
		let response = await axios.get("/api/item_list");
		items.value=response.data.items
	}

	const getDepartment = async () => {
        const response = await  axios.get("/api/department_list");
        department.value = response.data.department;
    }

	const getSupplier = async () => {
        const response = await  axios.get("/api/supplier_list");
        supplier.value = response.data.supplier;
    }

	const getBrand = async () => {
        const response = await  axios.get("/api/brand_list");
        brand.value = response.data.brand;
    }

	const getCatalog = async () => {
        const response = await  axios.get("/api/catalog_list");
        catalog.value = response.data.catalog;
    }

	const getItemname = async () => {
		if(props.item_id!=0){
			var item_id=props.item_id
		}else{
			var item_id=form.value.item_name
		}
        const response = await  axios.get("/api/get_itemname/"+item_id);
        itemname.value = response.data.item;
    }
	
	const getStockcard = async () => {
		if(props.variant_id!=0 || props.item_id!=0 || props.supplier_id!=0 || props.catalog_no!='null' || props.brand!='null' || props.department_id!=0){

			let response = await axios.get('/api/stockcard_item/'+props.variant_id+'/'+props.item_id+'/'+props.supplier_id+'/'+props.catalog_no+'/'+props.brand+'/'+props.department_id);
			// axios.get(`/api/all_stockcarditem_transactions/`+props.item_id+`/`+props.supplier_id+`/`+props.catalog_no+`/`+props.brand+`/`+props.department_id).then(function (response) {
				stockcard.value = response.data.stockcard
				var stockcard_disp= response.data.stockcard.sort((a, b) => new Date(a.date_created) - new Date(b.date_created))
				stockcard.value=stockcard_disp
				// var balance=response.data.balance.sort((a, b) => new Date(a.date_created) - new Date(b.date_created))
				// var balance_disp=balance
				var quantitysum=0;
				stockcard_disp.forEach((balances, index) => {
					if(balances.method=='Begbal' || balances.method=='Receive' || balances.method=='Restock' || balances.method=='Backorder'){
						quantitysum +=parseFloat(balances.quantity)
					}else if(balances.method=='Issuance' || balances.method=='Borrow' || balances.method=='Restock-Damaged'){
						quantitysum -=parseFloat(balances.quantity)
					}
					quantity.value[index]=parseFloat(quantitysum);
				});
				overall_qty.value=quantitysum
				getItemname()
				document.getElementById("showExport").style.display="block"
			// });
		}
	}

	const filter = () => {
		const formDetails = new FormData()
		formDetails.append('item', form.value.item_name)
		formDetails.append('supplier', form.value.supplier_name)
		formDetails.append('department', form.value.department)
		formDetails.append('catalog', form.value.catalog)
		formDetails.append('brand', form.value.brand)
		axios.post(`/api/all_stockcard_transactions`, formDetails).then(function (response) {
			// stockcard.value = response.data.stockcard
			// var stockcard_disp= response.data.stockcard.sort((a, b) => new Date(a.date_created) - new Date(b.date_created))
			var stockcard_disp= response.data.stockcard.sort((a, b) => new Date(a.date_created) - new Date(b.date_created))
			stockcard.value=stockcard_disp
			// var balance=response.data.balance.sort((a, b) => new Date(a.date_created) - new Date(b.date_created))
			// var balance_disp=balance
			var quantitysum=0;
			stockcard_disp.forEach((balances, index) => {
				if(balances.method=='Begbal' || balances.method=='Receive' || balances.method=='Restock' || balances.method=='Backorder'){
					quantitysum +=parseFloat(balances.quantity)
				}else if(balances.method=='Issuance' || balances.method=='Borrow' || balances.method=='Restock-Damaged'){
					quantitysum -=parseFloat(balances.quantity)
				}
				quantity.value[index]=parseFloat(quantitysum);
			});
			overall_qty.value=quantitysum
			getItemname()
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
							<h6 class="m-0 pt-1 font-bold uppercase">Stockcard</h6>
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
							<div class="flex justify-between pb-2 my-2 space-x-2">
								<div class="w-full">
									<select class="form-control border" v-model="form.item_name">
                                        <option value="">Select Item</option>
										<option v-for="i in items" :value="i.id" v-bind:key="i.id">{{ i.item_description }}</option>
                                    </select>
								</div>
								<div class="w-full">
									<select class="form-control border" v-model="form.supplier_name">
										<option value="">Select Supplier</option>
										<option v-for="s in supplier" v-bind:key="s.id" v-bind:value="s.id">{{ s.supplier_name}}</option>
									</select>
								</div>
							</div>
							<div class="flex justify-between pb-2 space-x-2">
								<div class="w-full">
									<select class="form-control border my-1" v-model="form.department">
										<option value="">Select Department</option>
										<option v-for="d in department" v-bind:key="d.id" v-bind:value="d.id">{{ d.department_name }}</option>
									</select>
								</div>
								<div class="w-full">
									<select class="form-control border my-1" v-model="form.catalog">
										<option value="">Select Catalog No</option>
										<option v-for="c in catalog" v-bind:key="c.catalog_no" v-bind:value="c.catalog_no">{{ c.catalog_no }}</option>
									</select>
								</div>
								<div class="w-full">
									<select class="form-control border my-1" v-model="form.brand">
										<option value="">Select Brand</option>
										<option v-for="b in brand" v-bind:key="b.brand" v-bind:value="b.brand">{{ b.brand }}</option>
									</select>
								</div>
								<button class="btn btn-sm btn-success" @click="filter()">
									<div class="flex justify-between space-x-2" >
										<FunnelIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"></FunnelIcon>
										<span>Generate Report</span>
									</div>
								</button>
							</div>
							<div>
							<hr class="mb-2">
							<div class="flex justify-between pb-2">
								<span class="font-bold text-xl text-blue-700">{{ itemname }}</span>
								<span class="bg-blue-600 rounded text-white px-4 py-1">Running Balance: <b>{{ overall_qty }}</b></span>
							</div>
							<hr class="border-dashed">
                            <div class="row" style="display: none;" id="showExport" v-if="(props.variant_id!=0 || props.item_id!=0 || props.supplier_id!=0 || props.catalog_no!='null' || props.brand!='null' || props.department_id!=0)">
                                <div class="col-lg-12 px-1">
                                    <div class="flex justify-end mb-3">
										<a :href="'/export-stockcarditems/'+((props.variant_id!='0') ? props.variant_id : '0')+'/'+((props.item_id!='0') ? props.item_id : '0')+'/'+((props.supplier_id!='0') ? props.supplier_id : '0')+'/'+((props.catalog_no!='') ? props.catalog_no : 'null')+'/'+((props.brand!='') ? props.brand : 'null')+'/'+((props.department_id!='0') ? props.department_id : '0'+'/'+((overall_qty!='0') ? overall_qty : '0'))" class="btn btn-sm btn-success">
                                            <div class="flex justify-between space-x-2" >
                                                <span>Export to Excel</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
							<div class="row" style="display: none;" id="showExport" v-else>
                                <div class="col-lg-12 px-1">
                                    <div class="flex justify-end mb-3">
                                        <a :href="'/export-stockcard/'+((form.item_name!='') ? form.item_name : '0')+'/'+((form.supplier_name!='') ? form.supplier_name : '0')+'/'+((form.department!='') ? form.department : '0')+'/'+((form.catalog!='') ? form.catalog : 'null')+'/'+((form.brand!='') ? form.brand : 'null'+'/'+((overall_qty!='0') ? overall_qty : '0'))" class="btn btn-sm btn-success">
                                            <div class="flex justify-between space-x-2" >
                                                <span>Export to Excel</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
							<div class="w-full border hover:!overflow-x-scroll overflow-x-hidden h-96 bg-white mb-4  p-2">
								<!-- <table class="table border mb-0"> -->
								<table id="main_table" class="display text-xs table-bordered nowrap" width="130%"> 
									<thead>
										<tr>
											<th class="border align-bottom text-center" width="6%">Date</th>
											<th class="border align-bottom" width="10%">PR Number</th>
											<th class="border align-bottom" width="10%">PO Number</th>
											<th class="border align-bottom" width="15%">Supplier</th>
											<th class="border align-top text-center">Catalog #</th>
											<th class="border align-top text-center">Brand</th>
											<th class="border align-top text-center">Department</th>
											<th class="border align-top text-center" width="7%">TUC</th>
											<th class="border align-top text-center">Method</th>
											<th class="border align-top text-center" width="5%">Qty</th>
											<th class="border align-top text-center">Notes</th>
											<th class="border align-top text-center !bg-yellow-100" width="5%">Running Balance</th>
										</tr>
									</thead>
									<!-- <template #column-11="props">
										{{ quantity[props.rowIndex] }}
									</template> -->
									<tbody>
										<tr class="">
											<td class="text-xs border text-center">09-16-24</td>
											<td class="text-xs border"></td>
											<td class="text-xs border"></td>
											<td class="text-xs border"></td>
											<td class="text-xs border"></td>
											<td class="text-xs border"></td>
											<td class="text-xs border"></td>
											<td class="text-xs border text-center"></td>
											<td class="text-xs border"></td>
											<td class="text-xs border text-center"></td>
											<td class="text-xs border text-center"></td>
											<td class="text-xs border text-center font-bold !bg-yellow-100"></td>
										</tr>
									</tbody>
								</table>
							</div>
							</div>
							<!-- <div class="flex justify-end p-2 border-t">
								<nav aria-label="Page navigation example">
									<TailwindPagination
										:data="departments"
										:limit="1"
										@pagination-change-page="getDepartment"
									/>
								</nav>
							</div> -->
						</div>
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>
