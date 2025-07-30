<script setup>
	import{ onMounted, ref } from "vue"
	import navigation from '@/layouts/navigation.vue';
	import { CheckCircleIcon, TrashIcon, LockClosedIcon, XMarkIcon, PencilSquareIcon, ArrowUturnLeftIcon, ExclamationTriangleIcon, CheckIcon  } from '@heroicons/vue/24/solid'
	import { useRouter } from "vue-router"

    const router = useRouter()

	let head = ref({
        id:''
    })
	

	let details = ref([])
	let items = ref([])
	let override_email = ref('')
	let override_password = ref('')
	let error = ref([])
	let password_error = ref([])
	let success = ref()
	let max = ref()

	const props = defineProps({
        id:{
            type:String,
            default:''
        }
    })

	onMounted(async () =>{
        getReceiveHead()
		getReceiveDetails()
		getLatestDetailNo(props.id)
		
    })

	
	const getReceiveHead = async () => {
		let response = await axios.get(`/api/get_receive_head/${props.id}`)
		head.value = response.data.head
	}

	const getReceiveDetails = async () => {
		let response = await axios.get(`/api/get_receive_details/${props.id}`)
		details.value = response.data.details
	}

	const getLatestDetailNo = async () => {
		let response = await axios.get(`/api/get_latest_detail_no/${props.id}`)
		max.value = response.data;
		
	}

	const editHead = (id) => {

		const formHead = new FormData()
		formHead.append('receive_date', head.value.receive_date)
		formHead.append('mrecf_no', head.value.mrecf_no)
		formHead.append('waybill_no', head.value.waybill_no)
		formHead.append('dr_no', head.value.dr_no)
		formHead.append('po_no', head.value.po_no)
		formHead.append('si_or', head.value.si_or)
		formHead.append('pcf', head.value.pcf)

		axios.post(`/api/edit_receive_head/${head.value.id}`, formHead).then(function (response) {
		
			// error.value=[]
			success.value='Successfully updated!'
			closeModalHead()
			getReceiveHead()
			document.getElementById("success").style.display="block"
			document.getElementById("error").style.display="none"
			setTimeout(() => {
				document.getElementById("success").style.display="none"
			}, 4000);
			}, function (err) {
				closeModalHead()
				// error.value=[]
				document.getElementById("error").style.display="block"
				if (err.response.data.errors.receive_date) {
					error.value.push(err.response.data.errors.receive_date[0])
				}
				if (err.response.data.errors.dr_no) {
					error.value.push(err.response.data.errors.dr_no[0])
				}
				if (err.response.data.errors.pcf) {
					error.value.push(err.response.data.errors.pcf[0])
				}
				setTimeout(() => {
				document.getElementById("error").style.display="none"
				}, 4000);
		});

	}	

	const closeTransaction = (id) => {
		
		if(confirm("Are you sure you want to close this transaction?")){
			axios.get(`/api/close_transaction/`+id).then(function (response){
				//console.log(response)
				router.push("/receive/print/"+id)
				//alert(response.data)
			});
		}

	}

	const cancelTransaction = (id) => {
			if(confirm("Are you sure you want to cancel transaction?")){
				
				axios.get(`/api/cancel_transaction/${id}`).then(function () {
					router.push('/receive')
				});
			}
		}

	const override_pass = () => {
		const formData=new FormData()
		formData.append('override_password', override_password.value)
		formData.append('override_email', override_email.value)
		axios.post('/api/password_checker/', formData).then(function (response) {
			// console.log(response.data.override_user_id)
			// console.log(response.data)
            if(response.data.success){
				// axios.get(`/api/get_max_detail/${props.id}`).then(response => {
				// 	router.push(`/receive/override/${props.id}`+'/'+response.data)
				// })
                router.push(`/receive/override/${props.id}`+'/'+response.data.override_user_id)
            } else {
                password_error.value = response.data.message;
            }
        })
	}



	const updateHead = ref(false)
	const hideModal = ref(true)
	const openModalHead = () => {
		updateHead.value = !updateHead.value
	}
	const closeModalHead = () => {
		updateHead.value = !hideModal.value
		updatepr.value = !hideModal.value
	}

	const updatepr = ref(false)
	const openUpdate = () => {
		updatepr.value = !updatepr.value
	}
	const closeUpdate = () => {
		updatepr.value = !hideModal.value
	}

	const onPrint= (id) => {
		router.push('/receive/print/'+id)
	}


</script>

<template>
	<div>
		<div class="col-lg-4 offset-lg-4">
			<div class="flex content-center">
				<div class="hide-animate" v-if="success" id="success">
					<div class="alert alert-success alert-top my-2">
						<div class="flex justify-start space-x-1">
							<div>
								<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></CheckCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Success!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ success }}</p>
							</div>
						</div>
					</div>
				</div>
				<!-- <p class="text-success" v-if="success">{{ success }}</p>
				<p class="text-danger" v-for="err in error" v-if="error.length > 0">{{ err }}</p> -->
				<div v-else id="success"></div>
				<div class="hide-animate" v-for="err in error" v-if="error.length > 0" id="error">
					<div class="alert alert-danger alert-top my-2" >
						<div class="flex justify-start space-x-2">
							<div>
								<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationCircleIcon>
							</div> 
							<div class="py-1">
								<h6 class="font-bold m-0">Error!</h6>
								<p class="text-sm m-0 text-gray-400"> {{ err }}</p>
							</div>
						</div>
					</div>
				</div>
				<div v-else id="error"></div>
			</div>
		</div>	
	</div>
    <navigation>
        <div class="container-fluid">
			<!-- BreadCrumb -->
			<div class="card mb-3">	
				<div class="flex justify-between content-center">
					<div class="flex justify-start space-x-3 ">
						<div class="">
							<!-- <a :href="'/receive/new_second/'+head.id+'/'+max" class="btn btn-secondary btn-xs btn-rounded">
								<ArrowUturnLeftIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></ArrowUturnLeftIcon>
							</a> -->
						</div>
						<div>
							<h6 class="m-0 pt-1 font-bold uppercase">Receive</h6>
						</div>
					</div>	
					<div class="pt-1">	
						<nav aria-label="breadcrumb" role="navigation">
							<ol class="breadcrumb adminx-page-breadcrumb">
								<li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
								<li class="breadcrumb-item"><a href="/receive">Receive</a></li>
								<li class="breadcrumb-item active" aria-current="page">Add New Receive</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>	
			
 			<div class="alert bg-emerald-500 text-white border-0 shadow-sm !mb-3" v-if="head.closed == 0">
				<div class="flex justify-start space-x-2 py-1">
					<div>
						<ExclamationTriangleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12"></ExclamationTriangleIcon>
					</div> 
					<div class="py-1">
						<h6 class="font-bold m-0 leading-none uppercase">Note </h6> 
						<span class="text-white">Please review the details before closing and printing this transaction. Once closed, you will not be able to edit this transaction.</span>
					</div>
				</div>
			</div> 

			<div class="card p-4 card-main-bg">
				<!-- <hr class="m-0 mb-2"> -->
				<div class="mb-3">
					<div class="row">
						<div class="col-lg-12">
							<table class="w-full table-bordersed mt-2 mb-2 ">
								<tr>
									<td class="pr-1" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ head.mrecf_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">MRIF NO.</span>
										</div>
									</td>
									<td class="px-1" width="15%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 leading-none">{{ head.receive_date }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">DATE</span>
										</div>
									</td>
									
									<td width="8%"></td>
									<td class="px-1  "  width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.dr_no }}</span>
										</div>
										<div class="flex justify-start">
											<span class="text-xs text-gray-500 leading-none pt-1">DR NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.po_no }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">PO NUMBER</span>
										</div>
									</td>
									<td class="px-1  " width="15%">
										<div class="flex justify-start">
											<span class="text-md uppercase font-bold text-gray-600 leading-none">{{ head.si_or }}</span>
										</div>
										<div class="flex justify-start" >
											<span class="text-xs text-gray-500 leading-none pt-1">SI/OR NUMBER</span>
										</div>
									</td>
									<td class="pr-1" width="20%">
										<div class="flex justify-start">
											<span class="text-lg uppercase font-bold text-gray-600 w-full leading-none">
												{{ head.waybill_no }}
											</span>
										</div>
										<div class="flex justify-start ">
											<span class="text-xs text-gray-500 leading-none pt-1">WAYBILL NO.</span>
										</div>
									</td>
									<td class="px-1  " width="2%">
										<span class="flex justify-center text-green-600">
											<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" v-if="head.pcf === 1"></CheckCircleIcon>
											<span v-else></span>
										</span>
										<div class="flex justify-center ">
											<span class="text-xs text-gray-500 leading-none pt-1">PCF</span>
										</div>
										
									</td>
									<td class="p-0 w-0" width="5%">
										<div class="flex justify-end">
											<button @click="openModalHead()" class="btn btn-xs btn-info btn-rounded" v-if="head.closed == 0">
												<PencilSquareIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3"></PencilSquareIcon>
											</button>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>	
				
				<div v-for="det in details" class="border-2 border-blue-400 mb-3 rounded ">
					<div class="row">
						<div class="col-lg-12">
							<div class="rounded">
								<div class="row">
									<div class="col-lg-12">
										<div class="p-1 rounded-t-md bg-gray-200"></div>
									</div>
								</div>
								<table width="100%" class="table-bordered !text-[13px]">
									<tr class="">
										<td class="bg-gray-100 text-gray-600 font-bold border-gray-200 border-r px-2 py-1" width="13%" >PR/JO/WH Stocks</td>
										<td class="px-2 font-bold" colspan="2" width="40%">{{ det.pr_no }}</td>
										<td class="bg-gray-100 text-gray-600 font-bold border-gray-200 border-r px-2 py-1" width="10%" >Inspected by</td>
										<td class="px-2" width="41%"> {{det.inspected_id }}</td>
									</tr>
									<tr>
										<td class="bg-gray-100 text-gray-600 font-bold border-gray-200 border-r px-2 py-1" width="10%" >Department</td>
										<td class="px-2" colspan="2">{{ det.department_id }}</td>
										<td class="bg-gray-100 text-gray-600 font-bold border-gray-200 border-r px-2 py-1" width="10%" >End-Use</td>
										<td class="px-2">{{ det.enduse_id }}</td>
									</tr>
									<tr>
										<td class="bg-gray-100 text-gray-600 font-bold border-gray-200 border-r px-2 py-1" width="10%" >Purpose</td>
										<td class="px-2" colspan="5">{{ det.purpose_id }}</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<table class="table-bordered text-[13px] mb-0 w-full" v-for="it in det.receive_items.items">
								<tr>
									<td class="px-0 py-0 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="5%">
										<span class="ml- px-2.5 py-1 bg-blue-500 mr-1 text-white font-bolder">{{ it.item_no }}</span>Supplier
									</td>
									<td class="px-2" colspan="9">{{ it.supplier_name }}</td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="4%">PN No</td>
									<td class="px-2" colspan="2" width="12%">{{ it.pn_no }}</td>
								</tr>
								<tr>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="5%">Item Desc</td>
									<td class="px-2 !text-base font-bold" colspan="9">{{ it.item_description}}</td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="4%">Cat No</td>
									<td class="px-2" colspan="2">{{ it.catalog_no }}</td>
								</tr>
								<tr>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="5%">Brand</td>
									<td class="px-2" colspan="9"> {{ it.brand }} </td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="4%">Serial No</td>
									<td class="px-2"  colspan="2" width="10%"> {{ it.serial_no }} </td>
								</tr>
								<tr>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="5%">Other Desc</td>
									<td class="px-2" colspan="7"></td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="4%">Color</td>
									<td class="px-2" width="10%">{{ it.color }}</td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="4%">Size</td>
									<td class="px-2"  colspan="2" width="10%">{{ it.size }}</td>
								</tr>
								<tr>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold" >Item Status</td>
									<td class="px-1 py-1 bg-gray-100 text-gray-600 font-bold text-center" width="5%">S/U + Others</td>
									<td class="px-1 py-1 bg-gray-100 text-gray-600 font-bold text-center" width="5%">Unit Cost</td>
									<td class="px-1 py-1 bg-gray-100 text-gray-600 font-bold text-center" width="5%">Exp Qty</td>
									<td class="px-1 py-1 bg-gray-100 text-gray-600 font-bold text-center" width="5%">Recv Cost</td>
									<td class="px-1 py-1 bg-gray-100 text-gray-600 font-bold text-center" width="5%">Total Cost</td>
									<td class="px-1 py-1 bg-gray-100 text-gray-600 font-bold text-center" width="3%">Currency</td>
									<td class="px-1 py-1 bg-gray-100 text-gray-600 font-bold text-center" width="4%" >UOM</td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold text-left">Barcode</td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold text-left">Expiration</td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold text-left" width="5%"> Location</td>
									<td class="px-2 py-1 bg-gray-100 text-gray-600 font-bold text-left">Replenish</td>
								</tr>
								<tr>
									<td class="px-2 py-1">{{ it.item_status }}</td>
									<td class="px-2 py-1 text-center">{{ it.shipping_cost }}</td>
									<td class="px-2 py-1 text-center">{{ it.unit_cost }}</td>
									<td class="px-2 py-1 text-center">{{ it.exp_quantity }}</td>
									<td class="px-2 py-1 text-center">{{ it.rec_quantity }}</td>
									<td class="px-2 py-1 text-center" >{{ parseFloat((it.unit_cost + it.shipping_cost) * it.rec_quantity).toFixed(2)}}</td>
									<td class="px-2 py-1 text-center">{{ it.currency }}</td>
									<td class="px-2 py-1 text-center">{{ it.uom }}</td>
									<td class="px-2 py-1 text-left"></td>
									<td class="px-2 py-1 text-left">{{ it.expiry_date }}</td>
									<td class="px-2 py-1 ">
										<div class="flex justify-center space-x-1"  v-if="it.location == 'local'" >
											<span>Local:</span>
											<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-emerald-500"></CheckCircleIcon>
										</div>
										<div class="flex justify-center space-x-1" v-if="it.location == 'manila'" >
											<span>Manila:</span>
											<CheckCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-emerald-500"></CheckCircleIcon>
										</div>
										<div v-else></div>
									</td>
									<td class="px-2 py-1">
										<span class="italic" v-if="it.pr_replenish == 1">Replenish: Yes</span> 
											<span class="italic" v-else>Replenish: No</span> 
									</td>
								</tr>
								<tr>
									<td class="px-2 py-1 py-1 bg-gray-100 text-gray-600 font-bold border-gray-200 border-r" width="5%">Remarks</td>
									<td class="px-2 py-1" colspan="11">{{ it.remarks }}</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="p-2 bg-gray-200">
							</div>
						</div>
					</div>
				</div>
				<hr class="border-dashed mb-0">
				<div class="pt-3 mb-2 flex justify-between space-x-10" v-if="head.closed == 0">
					<a :href="'/receive/new_second/'+head.id+'/'+max" type="submit" class="btn btn-sm bg-info text-white w-32">Go back and edit</a>
					<div class="flex justify-between space-x-1">
						<button  @click="cancelTransaction(head.id)" class="btn btn-sm btn-danger text-white w-40" >Cancel Transaction</button>
						<a :href="'/receive/print_draft/'+head.id+'/'"  class="btn btn-sm bg-orange-500 text-white w-40" >Print Draft</a>
						<button @click="closeTransaction(head.id)" type="submit" class="btn btn-sm btn-primary text-white w-52">Close & Print</button>
					</div>
				</div>
				<div class="pt-3 mb-2 flex justify-end space-x-1" v-if="head.closed == 1">
					
					<div class="flex justify-end space-x-1">
						<button type="submit" class="btn btn-sm btn-info w-44 text-white" @click="openUpdate()">
							<span class="flex justify-center space-x-2">
								<span>
									<LockClosedIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></LockClosedIcon>
								</span>
								<span>Update</span>
							</span>
						</button>
						<a href="#" @click="onPrint(head.id)" type="submit" class="btn btn-sm btn-primary w-52 text-white">Print</a>
					</div>
				</div>
			</div>
		</div>

		<!-- MODAL -->
		<div class="modal pt-5 px-3" :class="{ show:updateHead }">
			<div @click="closeModalHead" class="w-full h-full fixed"></div>
			<div class="modal__content w-1/3 p-4">
				<div class="row">
					<div class="col-lg-12 flex justify-between">
						<p class="mb-0 font-bold">Edit </p>
						<a href="#" class="text-gray-600" @click="closeModalHead">
							<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
						</a>
					</div>
				</div>
				<hr class="m-0 mb-2">
				<div class="modal_s_items">
					<div class="form-group mb-2">
						<label class="form-label">MrecF No.</label>
						<input type="text" class="form-control border" v-model="head.mrecf_no" readonly>
					</div>		
					<div class="form-group mb-2">
						<label class="form-label">Date</label>
						<input type="date" class="form-control border" disabled v-model="head.receive_date">
					</div>			
				
					<div class="form-group mb-2">
						<label class="form-label">DR Number</label>
						<input type="text" class="form-control border" v-model="head.dr_no">
					</div>	
				
					<div class="form-group mb-2">
						<label class="form-label">PO Number</label>
						<input type="text" class="form-control border" v-model="head.po_no">
					</div>	
				
					<div class="form-group mb-2">
						<label class="form-label">SI/OR Number</label>
						<input type="text" class="form-control border" v-model="head.si_or">
					</div>	
					<div class="form-group mb-2">
						<label class="form-label">Waybill No.</label>
						<input type="text" class="form-control border" v-model="head.waybill_no">
					</div>
					<div class="form-group mb-2">
						<label class="form-label mr-2 mb-0">PCF</label>
						<span class="pt-2">
							<input type="checkbox" v-model='head.pcf'  true-value="1" false-value="0" class="">
						</span>
					</div>
				</div> 
				<input type="hidden" class="form-control border" v-model="head.id">
				<hr class="m-0 mt-3 mb-3">
				<div class="flex justify-end">
					<button @click="editHead(head.id)" class="btn btn-info btn-rounded btn-sm w-32">Submit</button>
				</div>		
			</div>
		</div>
		<div class="modal pt-5 px-3" :class="{ show:updatepr }">
			<div @click="closeModalHead" class="w-full h-full fixed"></div>
			<div class="modal__content w-1/3 p-4">
				<div class="alert alert-danger" v-if="password_error.length > 0">
					<div class="flex justify-start space-x-2">
						<div>
							<ExclamationCircleIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"></ExclamationCircleIcon>
						</div> 
						<div> {{ password_error }} </div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 flex justify-between">
						<p class="mb-0 font-bold">Enter credentials to update </p>
						<a href="#" class="text-gray-600" @click="closeModalHead">
							<XMarkIcon fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"></XMarkIcon>
						</a>
					</div>
				</div>
				<hr class="m-0 mb-2">
				<div class="modal_s_items">
					<div class="form-group mb-2">
						<input type="email" class="form-control border" v-model="override_email"  placeholder="email@example.com">
					</div>
					<div class="form-group mb-2">
						<input type="password" class="form-control border" v-model="override_password" placeholder="Password">
					</div>	
				</div> 
				<div class="flex justify-end">
					<!-- <a :href="'/receive/override/'+head.id" class="btn btn-info btn-rounded btn-sm w-32">Override</a> -->
					<button class="btn btn-info btn-rounded btn-sm w-32" @click="override_pass()">Override</button>
				</div>		
			</div>
		</div>
    </navigation>
</template>
