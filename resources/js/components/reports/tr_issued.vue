<script setup>
	import { onMounted, ref } from "vue"
	import { useRouter } from "vue-router" 
	import navigation from '@/layouts/navigation.vue';
	import { PencilSquareIcon, Bars3Icon, PlusIcon, MagnifyingGlassIcon, ChevronLeftIcon, ChevronRightIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/solid'
    import 'datatables.net-dt/css/dataTables.dataTables.css';
    import 'datatables.net';
    onMounted(() => {
        $('#main_table').DataTable();
    });
    import $ from 'jquery'
	import jszip from 'jszip';
    import moment from 'moment'
    let rows = ref([])
    let form = ref({
        from_date:'',
        to_date:'',
        item_name:'',
        pr_no:'',
        category:'',
        subcategory:'',
        department:'',
        enduse:'',
        purpose:''
    })
    let items = ref([])
    let pr = ref([])
    let categories = ref([])
    let subcategories = ref([])
    let department = ref([])
    let enduse = ref([])
    let purpose = ref([])
    let pr_cost = ref('0')
    let wh_cost = ref('0')
    let wh_wo_cost = ref('0')
    let pr_wo_cost = ref('0')
    onMounted(async () =>{
        getItems()
        getPR()
        getCategory()
        getDepartment()
        getEnduse()
        getPurpose()
    })
    const generateReport = () => {
        const formData= new FormData()
        formData.append('from_date', form.value.from_date)
        formData.append('to_date', form.value.to_date)
        formData.append('item', form.value.item_name)
        formData.append('pr_no', form.value.pr_no)
        formData.append('category', form.value.category)
        formData.append('subcategory', form.value.subcategory)
        formData.append('department', form.value.department)
        formData.append('enduse', form.value.enduse)
        formData.append('purpose', form.value.purpose)
        axios.post("/api/all_issued_transactions",formData).then(function (response) {
            rows.value=response.data.issuearray
            pr_cost.value=response.data.pr_cost
            wh_cost.value=response.data.wh_cost
            pr_wo_cost.value=response.data.pr_wo_cost
            wh_wo_cost.value=response.data.wh_wo_cost
            document.getElementById("showExport").style.display="block"
        }, function (err) {
            alert('Error! please try again.')
        });
    }
    
    const getItems = async() => {
		let response = await axios.get("/api/item_list");
		items.value = response.data.items;
    }

    const getPR = async() => {
		let response = await axios.get("/api/pr_request_list");
		pr.value = response.data.pr_request;
    }

    const getCategory = async () => {
        const response = await  axios.get("/api/category_list");
        categories.value = response.data.category;
    }

     const getSubcategories= () =>{
     
        var id= document.getElementById("category").value;
       axios.get(`/api/get_subcategories/`+id).then(function (response) {
        subcategories.value = response.data
       })       
    }

    const getDepartment = async () => {
        const response = await  axios.get("/api/department_list");
        department.value = response.data.department;
    }

    const getEnduse = async () => {
        const response = await  axios.get("/api/enduse_list");
        enduse.value = response.data.enduse;
    }

    const getPurpose = async () => {
        const response = await  axios.get("/api/purpose_list");
        purpose.value = response.data.purpose;
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
							<h6 class="m-0 pt-1 font-bold uppercase">Issue Report</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Issue Report</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	

			<div class="row">
				<div class="col-md-12 col-lg-12 ">
					<div class="card">
                        <div class="row px-4 pt-2">
                            <div class="col-lg-12  px-1">
                                <span>Search Item:</span>
                            </div>
                        </div>
                        <div class="pt-3 px-4">
                            <div class="row">
                                <div class="col-lg-2 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">From:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <input type="date" class="form-control border my-1" v-model="form.from_date">
                                        </span>
                                    </div>										
                                </div>
                                <div class="col-lg-2 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">To:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <input type="date" class="form-control border my-1" v-model="form.to_date">
                                        </span>
                                    </div>										
                                </div>
                                <div class="col-lg-5 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">Item:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <select class="form-control border my-1" v-model="form.item_name">
                                                <option value="">Select Item</option>
                                                <option v-for="i in items" v-bind:key="i.id" :value="i.id" >{{ i.item_description }}</option>
                                            </select>
                                        </span>
                                    </div>										
                                </div>
                                <div class="col-lg-3 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">PR:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <select class="form-control border my-1" v-model="form.pr_no">
                                                <option value="">Select PR</option>
                                                <option v-for="p in pr" v-bind:key="p.pr_no" >{{ p.pr_no }}</option>
                                            </select>
                                        </span>
                                    </div>										
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">Category:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <select class="form-control border my-1" id="category" @change="getSubcategories()" v-model="form.category" >
                                                <option value="">Select Category</option>
                                                <option v-for="c in categories" v-bind:key="c.id" v-bind:value="c.id" >{{ c.category_name }}</option>
                                            </select>
                                        </span>
                                    </div>										
                                </div>
                                <div class="col-lg-4 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">Sub Category:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <select class="form-control border my-1" v-model="form.subcategory">
                                                <option value="">Select Sub Category</option>
                                                <option v-for="sc in subcategories" v-bind:key="sc.id" v-bind:value="sc.id">{{ sc.subcat_name }}</option>
                                            </select>
                                        </span>
                                    </div>										
                                </div>
                                <div class="col-lg-4 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">Department:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <select class="form-control border my-1" v-model="form.department">
                                                <option value="">Select Department</option>
                                                <option v-for="d in department" v-bind:key="d.id" v-bind:value="d.id">{{ d.department_name }}</option>
                                            </select>
                                        </span>
                                    </div>										
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">Enduse:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <select class="form-control border my-1" v-model="form.enduse">
                                                <option value="">Select Enduse</option>
                                                <option v-for="e in enduse" v-bind:key="e.id" v-bind:value="e.id">{{ e.enduse_name }}</option>
                                            </select>
                                        </span>
                                    </div>										
                                </div>
                                <div class="col-lg-6 px-1">
                                    <div class="form-group">
                                        <div class="flex justify-start ">
                                            <span class="text-xs text-gray-500 leading-none uppercase">Purpose:</span>
                                        </div>
                                        <span class="text-lg uppercase text-gray-700 w-full leading-none">
                                            <select class="form-control border my-1" v-model="form.purpose">
                                                <option value="">Select Purpose</option>
                                                <option v-for="p in purpose" v-bind:key="p.id" v-bind:value="p.id">{{ p.purpose_name }}</option>
                                            </select>
                                        </span>
                                    </div>										
                                </div>
                                <div class="col-lg-2 px-1">
                                    <div class="pt-3">
                                        <button class="btn btn-primary btn-block btn-sm"  @click="generateReport">Generate</button>
                                    </div>						
                                </div>
                            </div>
                            <hr class="border-dashed">
                            <div class="row" style="display: none;" id="showExport">
                                <div class="col-lg-12 px-1">
                                    <div class="flex justify-end mb-3">
                                        <a :href="'/export-issue/'+((form.from_date!='') ? form.from_date : 'null')+'/'+((form.to_date!='') ? form.to_date : 'null')+'/'+((form.item_name!='') ? form.item_name : '0')+'/'+((form.pr_no!='') ? form.pr_no : 'null')+'/'+((form.category!='') ? form.category : '0')+'/'+((form.subcategory!='') ? form.subcategory : '0')+'/'+((form.department!='') ? form.department : '0')+'/'+((form.enduse!='') ? form.enduse : '0')+'/'+((form.purpose!='') ? form.purpose : '0')" class="btn btn-sm btn-success">
                                            <div class="flex justify-between space-x-2" >
                                                <span>Export to Excel</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 px-1">
                                    <div class='w-full border hover:!overflow-x-scroll overflow-x-hidden h-96 bg-white mb-4  p-2'>
                                        <!-- <table class="text-xs table-bordered" width="280%"> -->
                                        <table width="100%" class="table-bordered">
                                            <tr>
                                                <td width="">Total Cost w/ PR:</td>
                                                <td width="20%" align="right" style="background: #fff4e2;font-weight: 450">{{ pr_cost }}</td>
                                                <td width="4%"><br></td>
                                                <td width="">Total Number of Items w/ PR w/o Cost:</td>
                                                <td width="20%" align="right" style="background: #fff4e2;font-weight: 450">{{ pr_wo_cost }}</td>
                                            </tr>
                                            <tr>
                                                <td>Total Cost of WH Stocks:</td>
                                                <td align="right" style="background: #fff4e2;font-weight: 450">{{ wh_cost }}</td>
                                                <td></td>
                                                <td>Total Number of Items from WH Stocks w/o Cost:</td>
                                                <td align="right" style="background: #fff4e2;font-weight: 450">{{ wh_wo_cost }}</td>
                                            </tr>
                                        </table>
                                        <br>
                                        <DataTable :data="rows" :options="options" class="display text-xs table-bordered nowrap" width="200%">
                                            <thead> 
                                                <tr class="bg-gray-200 font-bold sticky top-0 z-50">
                                                    <th class="px-2 py-1 text-center" width="3%">Issue Date</th>
                                                    <th class="px-2 py-1" width="5%">PO No.</th>
                                                    <th class="px-2 py-1" width="5%">MIF No.</th>
                                                    <th class="px-2 py-1" width="5%">PR No.</th>
                                                    <th class="px-2 py-1">Part No.</th>
                                                    <th class="px-2 py-1 sticky left-0 top-0 bg-gray-200 " width="10%">Item Description</th>
                                                    <th class="px-2 py-1" width="4%">Total Qty Issued</th>
                                                    <th class="px-2 py-1">UOM</th>
                                                    <th class="px-2 py-1" width="3%">Unit Cost</th>
                                                    <th class="px-2 py-1" width="3%">Currency</th>
                                                    <th class="px-2 py-1" width="3%">Total Cost</th>
                                                    <th class="px-2 py-1">Supplier</th>
                                                    <th class="px-2 py-1" width="8%">Department</th>
                                                    <th class="px-2 py-1">Enduse</th>
                                                    <th class="px-2 py-1" width="10%">Purpose </th>
                                                </tr>
                                            </thead>
                                        </DataTable>
                                            <!-- <tr class="hover:bg-yellow-100 bg-slate" v-for="r in rows">
                                                <td class="px-2 py-1 text-center">
                                                    {{ r.issuance_head.issuance_date}}
                                                </td>
                                                <td class="px-2 py-1">{{ r.issuance_head.pr_no }}</td>
                                                <td class="px-2 py-1">{{ r.issuance_head.mif_no }}</td>
                                                <td class="px-2 py-1">{{ r.items.pn_no }}</td>
                                                <td class="px-2 py-1 sticky left-0 bg-white">{{ r.item_description }}</td>
                                                <td class="px-2 py-1">{{ r.issued_qty }}</td>
                                                <td class="px-2 py-1" v-if="r.receive_items!=null">{{ r.receive_items.uom }}</td>
                                                <td class="px-2 py-1" v-else></td>
                                                <td class="px-2 py-1" v-if="r.receive_items!=null">{{ r.receive_items.unit_cost }}</td>
                                                <td class="px-2 py-1" v-else></td>
                                                <td class="px-2 py-1" v-if="r.receive_items!=null">{{ r.issued_qty * r.receive_items.unit_cost }}</td>
                                                <td class="px-2 py-1" v-else>{{ r.issued_qty * 0 }}</td>
                                                <td class="px-2 py-1" v-if="r.receive_items!=null">{{ r.receive_items.supplier_name }}</td>
                                                <td class="px-2 py-1" v-else></td>
                                                <td class="px-2 py-1">{{ r.issuance_head.department_name}}</td>
                                                <td class="px-2 py-1">{{ r.issuance_head.enduse_name }}</td>
                                                <td class="px-2 py-1">{{ r.issuance_head.purpose_name }}</td>
                                            </tr>
                                        </table> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        
					</div>
				</div>
			</div>
		</div>
    </navigation>
</template>