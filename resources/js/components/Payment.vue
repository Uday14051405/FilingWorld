<template>

  <div class="booking-list-content">
                <h5 class="mt-5 mb-0 text-capitalize">{{ $t('landingpage.payment_option') }}</h5>
                <div class="mt-0">
                    <form @submit="formSubmit">
                      <template v-if="!isChildComponentVisible">
                      <!-- <input type="hidden" name="_token" :value="csrfToken"> -->
                      <input type="hidden" name="_token" :value="csrfToken">

                        <h6 class="mb-2 mt-3 text-capitalize">{{ $t('messages.payment_method') }}</h6>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                          
                            <div class="form-check"  v-if="isStripeEnabled">
                              <input class="form-check-input" type="radio" name="payment_method" v-model="payment_method" id='stripe' value='stripe' :checked="payment_method == 'stripe'"/>

                              <label class="form-check-label h6 fw-normal text-capitalize"
                                  for="stripe">{{$t('messages.stripe')}}</label>
                            </div>



                            <div class="form-check"  v-if="isRazorPayEnabled">
                              <input class="form-check-input" type="radio" name="payment_method" v-model="payment_method" id='razorpay' value='razorpay' :checked="payment_method == 'razorpay'"/>

                              <label class="form-check-label h6 fw-normal text-capitalize"
                                  for="stripe">{{$t('messages.razorpay')}}</label>
                            </div>

                            <!-- <div class="form-check">
                              <input class="form-check-input" type="radio" name="payment_method" v-model="payment_method" id='wallet' value='wallet' :checked="payment_method == 'wallet'"/>

                              <label class="form-check-label h6 fw-normal text-capitalize"
                                for="wallet">{{$t('messages.wallet')}}</label>
                            </div> -->
                            <div class="form-check" v-if="is_enable_advance_payment == 0 && isCashEnabled">
                              <input class="form-check-input" type="radio" name="payment_method" v-model="payment_method" id='cash' value='cash' :checked="payment_method == 'cash'"/>

                              <label class="form-check-label h6 fw-normal text-capitalize"
                                for="cash">{{$t('messages.cash')}}</label>
                            </div>
                          
                        </div>
                        <!-- <p>{{ $t('messages.wallet_balance') }}: {{formatCurrencyVue(wallet_amount)}}</p> -->
                        <div class="mt-3">
                            <div class="d-inline-flex align-items-center flex-wrap gap-3">
                                <button class="btn btn-primary" type="submit"> <span v-if="IsLoading==1" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <span v-else>{{ $t('landingpage.Proceed_To_Payment') }}</span></button>
                            </div>
                        </div>
                      </template> 
                        <component :is="currentComponent" :service="service" :booking_id="booking_id" :discount="discount" :advance_payment_amount="advance_payment_amount" :customer_id="customer_id" :wallet_amount = "wallet_amount" v-if="isChildComponentVisible" />
                    </form>
                </div>
            </div>
</template>


<script setup>
import { ref, defineProps,computed,onMounted} from 'vue';
import * as yup from 'yup'
import { useField, useForm } from 'vee-validate'
import { GET_PAYMENT_METHOD, GET_STRIPE_PAYMENT_URL,WALLET_PAYMENT_API,PAYMENT_GATEWAY_LIST} from '../data/api'; 
import Swal from 'sweetalert2';
import { confirmcancleSwal, confirmcancleWallet} from '../data/utilities'; 
import Wallet from '../components/Wallet.vue';
const props = defineProps(['is_enable_advance_payment','booking_id','customer_id','discount','total_amount','advance_payment_amount','wallet_amount']);
console.log(props.is_enable_advance_payment)
const paymentGatewayList = ref([]); 
const fetchPaymentGatewayList = async () => {
  try {
    const response = await fetch(PAYMENT_GATEWAY_LIST);
    if (response.ok) {
      const responseData = await response.json();
      paymentGatewayList.value = Array.isArray(responseData.data) ? responseData.data : [];
    } else {
      throw new Error('Failed to fetch payment gateway list');
    }
  } catch (error) {
    console.error('Error fetching payment gateway list:', error);
  }
};

onMounted(async () => {
  await fetchPaymentGatewayList();
  setFormData(defaultData());
});

const isStripeEnabled = computed(() => {
  return Array.isArray(paymentGatewayList.value) && paymentGatewayList.value.some(gateway => gateway.type === 'stripe' && gateway.status === 1);
});

const isCashEnabled = computed(() => {
  return Array.isArray(paymentGatewayList.value) && paymentGatewayList.value.some(gateway => gateway.type === 'cash' && gateway.status === 1);
});

const isRazorPayEnabled = computed(() => {
  return Array.isArray(paymentGatewayList.value) && paymentGatewayList.value.some(gateway => gateway.type.toLowerCase() === 'razorpay' && gateway.status === 1);
});


console.log(isCashEnabled);
   
 

const IsLoading=ref(0);

const defaultData = () => ({
  payment_method: 'razorpay',
});

const setFormData = (data) => {
  resetForm({ values: { payment_method: data.payment_method } });
};

const validationSchema = yup.object({

})

const { handleSubmit, errors, resetForm } = useForm({});

const { value: payment_method } = useField('payment_method');


const errorMessages = ref({})

const formSubmit = handleSubmit(async(values) => {
  values.booking_id=props.booking_id;
  values.customer_id=props.customer_id;
  values.discount=props.discount;
  values.payment_type = values.payment_method;
  values.wallet_amount = props.wallet_amount;
  if(props.advance_payment_amount !=null){
    values.total_amount=props.advance_payment_amount;
    values.type='advance_payment';
  }else{
    values.total_amount=props.total_amount;
    values.type='full_payment'
  }
  values.total_amount = Number(values.total_amount).toFixed(2);
  
  if(values.payment_type == 'wallet'){
    if(values.wallet_amount > 0 && values.total_amount < values.wallet_amount){
    IsLoading.value=1
    const title='Confirm Payment'
    const subtitle='Do you want to pay with Wallet ?'
    values.txn_id = `#${props.booking_id}`
    if(values.type == 'advance_payment'){
      values.total_amount=props.advance_payment_amount;
      values.advance_payment_amount=props.advance_payment_amount;
      values.payment_status = 'advanced_paid'; 
    }else{
      values.total_amount=props.total_amount;
      values.payment_status = 'paid';
    }
    confirmcancleSwal({ title: title, subtitle:subtitle }).then(async(result) => {
      IsLoading.value=0
      if (!result.isConfirmed) return
      IsLoading.value=1;
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const response = await fetch(WALLET_PAYMENT_API, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': csrfToken,
                },
                body:JSON.stringify(values),
            });
            if(response.ok){
              IsLoading.value=0

         const responseData = await response.json();
          Swal.fire({
          title: 'Done',
          text: responseData.message,
          icon: 'success',
          iconColor: '#5F60B9'
        }).then((result) => {

            if (result.isConfirmed) {
               const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
               window.location.href = baseUrl + '/booking-list';
             }

          })
 
            }else{
              IsLoading.value=0;
              Swal.fire({
                title: 'Error',
                text: 'Something Went Wrong!',
                icon: 'error',
                iconColor: '#5F60B9'
              }).then((result) => {

              })
            }
      })
    }else{
    const title='Do you want to Top Up your Wallet now?';
    confirmcancleWallet({ title: title}).then(async(result) => {
        IsLoading.value=0
      if (!result.isConfirmed) return
      IsLoading.value=1;
        showChildComponent();
      });
    }
    
  } else if (values.payment_type === 'cash') {
    IsLoading.value = 1;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const response = await fetch(WALLET_PAYMENT_API, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({
        ...values,
        payment_status: 'pending',
        txn_id: `#${props.booking_id}_cash`,
      }),
    });

    if (response.ok) {
      const responseData = await response.json();
      IsLoading.value = 0;

      Swal.fire({
        title: 'Success',
        text: 'Payment recorded successfully!',
        icon: 'success',
        iconColor: '#5F60B9',
      }).then(() => {
        const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
        window.location.href = baseUrl + '/booking-list';
      });
    } else {
      IsLoading.value = 0;

      Swal.fire({
        title: 'Error',
        text: 'Failed to record cash payment!',
        icon: 'error',
        iconColor: '#5F60B9',
      });
    }
  }  else if (values.payment_type === 'razorpay') { 

  
    payWithRazorpay(values);

  }
  else{
     

      IsLoading.value=1;
      
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

      const response = await fetch(GET_PAYMENT_METHOD, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
            },
            body:JSON.stringify(values),
        });
        if(response.ok){

          IsLoading.value=0;

          const responseData = await response.json();

          if(responseData.payment_geteway_data != null ){

            Openstripepayment(responseData)
    
          }else{

            IsLoading.value=0;

            Swal.fire({
              title: 'Error',
              text: 'check Your Stripe key Integration !',
              icon: 'error',
              iconColor: '#5F60B9'
            }).then((result) => {
    
            })

          }

        } else {

          IsLoading.value=0;

            Swal.fire({
              title: 'Error',
              text: 'Something Went Wrong!',
              icon: 'error',
              iconColor: '#5F60B9'
            }).then((result) => {
    
            })
        }
  }

  });

  const payWithRazorpay = (paymentData) => {

  IsLoading.value = 1;

  const options = {
    key: "rzp_test_4sALlNpMBPOdCX",
    amount: paymentData.total_amount * 100,
    currency: "INR",
    name: "Payment",
    description: "Booking Payment",
    handler: async function (response) {
      IsLoading.value = 0;
      Swal.fire({ title: "Success", text: "Payment Successful!", icon: "success" });

      savePaymentDetails(paymentData, response.razorpay_payment_id);
    },
    prefill: {
      email: "user@example.com",
      contact: "9999999999"
    },
    theme: {
      color: "#3399cc"
    }
  };

  const razorpay = new window.Razorpay(options);
  razorpay.open();
};


const savePaymentDetails = async (paymentData, txn_id) => {
  paymentData.txn_id = txn_id;
  paymentData.payment_status = "advanced_paid";

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  const headers = {
    "Content-Type": "application/json; charset=utf-8",
    "Accept": "application/json; charset=utf-8",
    "Language-Code": "en",
    "Cache-Control": "no-cache",
    "Access-Control-Allow-Headers": "*",
    "Access-Control-Allow-Origin": "*",
    'X-CSRF-TOKEN': csrfToken,
  };

  try {
    const response = await fetch(WALLET_PAYMENT_API, {
      method: "POST",
      headers,
      body: JSON.stringify(paymentData)
    });

    const responseData = await response.json();

    if (response.ok) {
      Swal.fire({ title: "Success", text: responseData.message, icon: "success" });
      window.location.href = "/booking-list";
    } else {
      Swal.fire({ title: "Error", text: "Failed to save payment!", icon: "error" });
    }
  } catch (error) {
    console.error("Error saving payment details:", error);
    Swal.fire({ title: "Error", text: "Something went wrong!", icon: "error" });
  }
};

 const Openstripepayment = async(data) => {

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  
  const res= await fetch(GET_STRIPE_PAYMENT_URL, {
           method: 'POST',
           headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
           },
           body:JSON.stringify(data),
        });
        if(res.ok){

          const responseData = await res.json();

          if(responseData.message){

               Swal.fire({
                 title:'Error',
                 text: responseData.message,
                 icon: 'error', 
                 iconColor: '#5F60B9'
               }).then((result) => {
       
               })
          }else{
              window.location.href = responseData.url;
          }
   

        }else{

            Swal.fire({
              title: 'Error',
              text: 'Something Went Wrong!',
              icon: 'error',
              iconColor: '#5F60B9'
            }).then((result) => {
    
            })
        }


}

const formatCurrencyVue = (value) => {

if(window.currencyFormat !== undefined) {
  return window.currencyFormat(value)
}
return value
}
 
const isChildComponentVisible = ref(false);
const currentComponent = ref(null);

const showChildComponent = () => {
  currentComponent.value = Wallet;
  isChildComponentVisible.value = true;
};

</script>
