<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'
import { initializePaddle } from '@paddle/paddle-js';
import { Check, Minus, Zap } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { plans as plansRoute, checkout as checkoutRoute } from '@/routes/billing';

type Plan = {
    id: number;
    paddle_id: string;
    name: string;
    price: number;
    yearly_offer_percentage: number;
    rate_limit: number;
    agent_limit: number;
    members_limit: number;
    credit_limit: number;
    free_limit: number;
    extra_credit: number;
};

const props = defineProps<{
    plans: Plan[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Plans & Billing', href: plansRoute() },
];

const billingCycle = ref<'monthly' | 'yearly'>('monthly');

const features = [
    { key: 'credit_limit', label: 'Monthly Credits' },
    { key: 'free_limit', label: 'Free Credits' },
    { key: 'extra_credit', label: 'Bonus Credits' },
    { key: 'agent_limit', label: 'AI Agents' },
    { key: 'members_limit', label: 'Workspace Members' },
    { key: 'rate_limit', label: 'Requests / min' },
] as const;

function formatValue(val: number, key: string): string {
    if (val === -1) return 'Unlimited';
    if (key === 'rate_limit' && val === 0) return '10';
    return val.toLocaleString();
}

function yearlyPrice(plan: Plan): string {
    if (plan.price === 0) return 'Free';
    const discounted = plan.price * (1 - plan.yearly_offer_percentage / 100);
    return `$${discounted.toFixed(0)}`;
}

function monthlyPrice(plan: Plan): string {
    if (plan.price === 0) return 'Free';
    return `$${plan.price}`;
}

const displayedPlans = computed(() => props.plans);

const popularPlan = 'pro-plus';
const checkout = async (plan_id: number) => {
    const $response = await fetch(checkoutRoute().url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            plan_id: plan_id,
            billing_cycle: billingCycle.value,
        }),
    });
    const response = await $response.json();
    console.log(response);
    if (response.checkout && response.client_token) {
        const paddle = await initializePaddle({
            token: response.client_token,
            pwCustomer: {
                id: response.checkout.customer.id,
            },
            eventCallback: function (data) {
                console.log(data);
                if (data.name === "checkout.completed") {

                    console.log("Payment successful!");
                    // You can redirect
                    window.location.href = "/dashboard";
                    // OR refresh subscription status
                    // OR show success message
                }
            },
            environment: 'sandbox',
        });
        console.log(paddle);
        if (paddle) {
            paddle.Checkout.open({
                items: response.checkout.items,
                customer: {
                    ...response.checkout.customer,
                }
            });
            console.log("paddle checkout open");

        }

    }
}
</script>

<template>

    <Head title="Plans & Billing" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 overflow-x-auto p-6">

            <!-- Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Choose your plan
                </h1>
                <p class="mt-2 text-gray-500 dark:text-gray-400">
                    Scale as you grow. Upgrade or downgrade anytime.
                </p>

                <!-- Monthly / Yearly toggle -->
                <div
                    class="mt-6 inline-flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-100 p-1 dark:border-white/10 dark:bg-white/5">
                    <button :class="[
                        'rounded-lg px-4 py-1.5 text-sm font-medium transition-all',
                        billingCycle === 'monthly'
                            ? 'bg-white text-gray-900 shadow dark:bg-white/10 dark:text-white'
                            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200',
                    ]" @click="billingCycle = 'monthly'">
                        Monthly
                    </button>
                    <button :class="[
                        'flex items-center gap-2 rounded-lg px-4 py-1.5 text-sm font-medium transition-all',
                        billingCycle === 'yearly'
                            ? 'bg-white text-gray-900 shadow dark:bg-white/10 dark:text-white'
                            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200',
                    ]" @click="billingCycle = 'yearly'">
                        Yearly
                        <span
                            class="rounded-full bg-emerald-100 px-1.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
                            Save up to 30%
                        </span>
                    </button>
                </div>
            </div>

            <!-- Plan cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="plan in displayedPlans" :key="plan.id" :class="[
                    'relative flex flex-col rounded-2xl border p-6 transition-shadow hover:shadow-lg',
                    plan.paddle_id === popularPlan
                        ? 'border-violet-500 bg-violet-50 shadow-violet-100 dark:bg-violet-950/30 dark:shadow-none'
                        : 'border-gray-200 bg-white dark:border-white/10 dark:bg-white/5',
                ]">
                    <!-- Popular badge -->
                    <div v-if="plan.paddle_id === popularPlan" class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span
                            class="flex items-center gap-1 rounded-full bg-violet-600 px-3 py-1 text-xs font-semibold text-white shadow">
                            <Zap class="size-3" />
                            Most Popular
                        </span>
                    </div>

                    <!-- Plan name -->
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ plan.name }}
                        </h2>

                        <!-- Price -->
                        <div class="mt-2 flex items-end gap-1">
                            <span class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                                {{ billingCycle === 'monthly' ? monthlyPrice(plan) : yearlyPrice(plan) }}
                            </span>
                            <span v-if="plan.price > 0" class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                / mo
                            </span>
                        </div>

                        <!-- Yearly saving badge -->
                        <div v-if="billingCycle === 'yearly' && plan.yearly_offer_percentage > 0" class="mt-1">
                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                {{ plan.yearly_offer_percentage }}% off yearly
                            </span>
                        </div>
                    </div>

                    <!-- CTA -->
                    <button @click="checkout(plan.id)" :class="[
                        'mb-6 w-full rounded-xl py-2.5 text-sm font-semibold transition-all',
                        plan.paddle_id === popularPlan
                            ? 'bg-violet-600 text-white hover:bg-violet-700 active:bg-violet-800'
                            : plan.price === 0
                                ? 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-white/20 dark:text-gray-200 dark:hover:bg-white/10'
                                : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-white/20 dark:text-gray-200 dark:hover:bg-white/10',
                    ]">
                        {{ plan.price === 0 ? 'Current plan' : 'Get started' }}
                    </button>

                    <!-- Divider -->
                    <div class="mb-4 border-t border-gray-200 dark:border-white/10" />

                    <!-- Feature rows -->
                    <ul class="flex flex-col gap-3">
                        <li v-for="feature in features" :key="feature.key"
                            class="flex items-center justify-between gap-2 text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ feature.label }}</span>
                            <span :class="[
                                'flex items-center gap-1 font-semibold',
                                plan[feature.key] === -1
                                    ? 'text-emerald-600 dark:text-emerald-400'
                                    : plan[feature.key] === 0
                                        ? 'text-gray-400 dark:text-gray-600'
                                        : 'text-gray-900 dark:text-white',
                            ]">
                                <Check v-if="plan[feature.key] === -1" class="size-4" />
                                <Minus v-else-if="plan[feature.key] === 0" class="size-4" />
                                {{ formatValue(plan[feature.key], feature.key) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
