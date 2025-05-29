<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BusinessSettingCollection;
use App\Models\BusinessSetting;
use App\Traits\ResponseAPI;

class BusinessSettingController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        return new BusinessSettingCollection(BusinessSetting::all());
    }

    public function about()
    {
        try {
            $faq = [
                "title" => "About Us",
                "content" => "<p>Welcome to <strong>LifeOk Shop</strong>, your one-stop destination for quality products designed to make life more convenient, stylish, and enjoyable. Established with a passion for bringing the best in everyday essentials, we are committed to offering a wide range of items that cater to diverse lifestyles, from home essentials and fashion to electronics and beauty products.</p><p>At <strong>LifeOk Shop</strong>, we believe that shopping should be more than just a transaction—it should be an experience. That’s why we focus on providing an exceptional online shopping environment where customers can discover high-quality products, browse through curated collections, and enjoy a seamless purchasing journey. Our mission is to ensure that every product we offer adds value to your life, whether it’s enhancing your comfort at home, updating your wardrobe, or gifting something special to a loved one.</p>",
                "why_shop_with_us" => "<h2>Why Shop with Us?</h2><ul><li><strong>Quality Assurance:</strong> We take pride in offering products that meet the highest standards of quality and functionality. Our team carefully selects each item, ensuring that it passes our rigorous quality checks before it reaches you.</li><li><strong>Affordable Prices:</strong> We strive to make great products accessible to everyone. With regular deals, discounts, and competitive pricing, you can enjoy the best without breaking the bank.</li><li><strong>Customer-Centric Approach:</strong> Your satisfaction is our top priority. From easy navigation on our website to fast and secure delivery, we aim to provide an enjoyable shopping experience. Our dedicated customer support team is always ready to assist with any questions or concerns you may have.</li><li><strong>Diverse Range of Products:</strong> Whether you're looking for home decor, fashion-forward clothing, the latest gadgets, or personal care products, <strong>LifeOk Shop</strong> has something for everyone. Our diverse collection is updated regularly to keep up with the latest trends and customer needs.</li><li><strong>Sustainability Commitment:</strong> We care about the environment and aim to promote eco-friendly products and packaging whenever possible. By shopping with us, you can contribute to a more sustainable future.</li></ul>",
                "vision" => "<h2>Our Vision</h2><p>At <strong>LifeOk Shop</strong>, we envision a world where shopping online is not only convenient but also a source of inspiration. Our goal is to create a platform where you can discover products that improve your life, whether its through innovation, style, or comfort. We are dedicated to growing our collection and continuously improving our service to bring you the best shopping experience possible.</p>"
            ];
            return $this->success("Successfully fetched About", $faq);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function getContactUs()
    {
        try {
            $contactUsData = [
                'title' => 'Office Address',
                'phone' => '01623458456',
                'email' => 'lifeokshop@gmail.com',
                'address' => 'বাড়ি: ১/১৩ ইকবাল রোড মোহাম্মদপুর, ব্লক - এ ( পানির পাম্প এর সামনে এবং মাঠের প্রথম গেট ) ইকবাল রোড - মোহাম্মদ পুর ঢাকা  -ফোন- 09639246270 , পাইকারীর জন্য',
                'lat' => 23.7594,
                'long' => 90.3748,
                'Facebook' => 'https://www.facebook.com/liifeokshop',
                'Instagram' => 'https://www.instagram.com/lifeokeyvs',
                'TickTok' => 'https://www.tiktok.com/@lifeokeyvs'
            ];
            return $this->success("Successfully fetched Contact us", $contactUsData);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function getShowroomLocation()
    {
        try {

            $showrooms = [
                [
                    'title' => 'Mirpur Showroom',
                    'phone' => '01760 18 32 11',
                    'email' => 'lifeokshop@gmail.com',
                    'address' => 'LIFE OK CITY - মিরপুর কমার্স কলেজের বিপরীত পাশে ,চিড়িয়ানা খানা রোড ,(মিরপুর ২)',
                    'lat' => 23.7808875,
                    'long' => 90.2792371
                ],
                [
                    'title' => 'Mohammadpur Showroom',
                    'phone' => '01760 18 32 11',
                    'email' => 'lifeokshop@gmail.com',
                    'address' => 'LIFE OK CITY - এন ২৮ , নুরজাহান রোড , বলক # ডি মোহা্মদপুর ঢাকা, (নিয়ার মোহা্মদপুর স্টট কলেজ , মোহাম্মদপুর সরকরি প্রাথমি বিদ্যালয় , বাইতুস সূজুদ জামে মসজিদ নুরজাহান রোড, মোহাম্মদপু বাসস্ট্যা্ড থেকে একট ভিতরে)',
                    'lat' => 23.7594,
                    'long' => 90.3748
                ],
              [
                    'title' => 'Bogra Showroom',
                    'phone' => '01760 18 32 11',
                    'email' => 'lifeokshop@gmail.com',
                    'address' => 'LIFE OKEY CITY - জলেশ্বরীতলা রাখি ম্যানশন - শহীদ আব্দুল জব্বার রোড, জলেশ্বরীতলা, বগুড়া ( প্রি-ক্যাডেট স্কুলের অপজিট সাইড )',
                    'lat' => 24.84477,
                    'long' => 89.3748
                ]
            ];
            return $this->success("Successfully fetched Showrooms", $showrooms);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function getDeliveryCharge()
    {
        try {
            $data = [
                [
                    'title' => 'Insite Dhaka',
                    'amount' => 100
                ],
                [
                    'title' => 'Outside Dhaka',
                    'amount' => 150
                ]
            ];
            return $this->success("Successfully fetched delivery charge", $data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
