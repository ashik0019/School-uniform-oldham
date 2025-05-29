<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PolicyCollection;
use App\Models\Policy;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    use ResponseAPI;
    public function sellerPolicy()
    {
        return new PolicyCollection(Policy::where('name', 'seller_policy')->get());
    }

    public function supportPolicy()
    {
        return new PolicyCollection(Policy::where('name', 'support_policy')->get());
    }

    public function returnPolicy()
    {
        try {
            $Data =  new  PolicyCollection(Policy::where('name', 'return_policy')->get());
            return $this->success("Successfully fetched return policy", $Data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function privacyPolicy()
    {
        try {
            $Data =  new  PolicyCollection(Policy::where('name', 'privacy_policy')->get());
            return $this->success("Successfully fetched privacy policy", $Data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function termsPolicy()
    {
        try {
            $Data =  new  PolicyCollection(Policy::where('name', 'terms')->get());
            return $this->success("Successfully fetched terms & conditions", $Data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function faqPolicy()
    {
        try {
            $faq = [
                [
                    'question' => 'What is your return policy?',
                    'answer' => 'You can return any item within 30 days of purchase for a full refund or exchange, provided it is in original condition.'
                ],
                [
                    'question' => 'How long does shipping take?',
                    'answer' => 'Standard shipping usually takes 5-7 business days, while express shipping takes 2-3 business days.'
                ],
                [
                    'question' => 'Do you offer international shipping?',
                    'answer' => 'Yes, we ship internationally. Delivery times and shipping costs will vary depending on the destination.'
                ],
                [
                    'question' => 'How can I track my order?',
                    'answer' => 'Once your order is shipped, you will receive a tracking number via email to track your package.'
                ],
                [
                    'question' => 'Can I cancel or modify my order?',
                    'answer' => 'You can cancel or modify your order within 24 hours of placing it. After that, it will be processed and shipped.'
                ],
                [
                    'question' => 'What payment methods do you accept?',
                    'answer' => 'We accept major credit cards, PayPal, and Apple Pay for online orders.'
                ],
            ];
            return $this->success("Successfully fetched FQA", $faq);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
