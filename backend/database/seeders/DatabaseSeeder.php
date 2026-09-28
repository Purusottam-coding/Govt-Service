<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Notice;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. प्रशासक खाता
        $admin = User::firstOrCreate(
            ['email' => 'admin@gov.np'],
            [
                'name' => 'प्रणाली प्रशासक',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '९८४०-०१२३४५',
                'address' => 'सिंहदरबार, काठमाडौं, नेपाल',
            ]
        );

        // 2. नागरिक परीक्षण खाता
        $citizen = User::firstOrCreate(
            ['email' => 'citizen@test.np'],
            [
                'name' => 'राम बहादुर श्रेष्ठ',
                'password' => Hash::make('password'),
                'role' => 'citizen',
                'phone' => '९८४१-०१२३४६',
                'address' => 'बालाजु, काठमाडौं, नेपाल',
            ]
        );

        // 3. मन्त्रालय तथा विभागहरू
        $deptTransport = Department::firstOrCreate(
            ['email' => 'transport@gov.np'],
            [
                'name' => 'यातायात व्यवस्था विभाग',
                'description' => 'सवारी चालक अनुमतिपत्र, सवारी साधन दर्ता, सडक परमिट, र यातायात नियमनसम्बन्धी कार्यहरू सञ्चालन गर्दछ।',
                'phone' => '०१-४२११५४०',
                'status' => true,
            ]
        );

        $deptImmigration = Department::firstOrCreate(
            ['email' => 'passport@gov.np'],
            [
                'name' => 'राहदानी विभाग (अध्यागमन)',
                'description' => 'राहदानी जारी, भिसा प्रशोधन, नागरिकता प्रमाणीकरण, र यात्रा कागजातसम्बन्धी सेवाहरू प्रदान गर्दछ।',
                'phone' => '०१-४४१४३३६',
                'status' => true,
            ]
        );

        $deptCivil = Department::firstOrCreate(
            ['email' => 'civilreg@gov.np'],
            [
                'name' => 'नागरिक दर्ता विभाग (जिल्ला प्रशासन)',
                'description' => 'जन्म दर्ता, विवाह दर्ता, मृत्यु दर्ता, र नागरिकता प्रमाणपत्रसम्बन्धी सेवाहरू व्यवस्थापन गर्दछ।',
                'phone' => '०१-४२११७८३',
                'status' => true,
            ]
        );

        $deptHousing = Department::firstOrCreate(
            ['email' => 'housing@gov.np'],
            [
                'name' => 'नगरपालिका तथा भवन निर्माण विभाग',
                'description' => 'निर्माण अनुमति, जग्गा प्रयोग स्वीकृति, सार्वजनिक आवास निवेदन, र सम्पत्ति दर्तासम्बन्धी कार्यहरू गर्दछ।',
                'phone' => '०१-४२११४५१',
                'status' => true,
            ]
        );

        $deptBusiness = Department::firstOrCreate(
            ['email' => 'commerce@gov.np'],
            [
                'name' => 'वाणिज्य तथा आपूर्ति विभाग',
                'description' => 'व्यापारिक व्यवसाय दर्ता, व्यापार इजाजत, कर अनुपालन प्रमाणपत्र, र व्यावसायिक परमिटसम्बन्धी सेवाहरू।',
                'phone' => '०१-४२११०१७',
                'status' => true,
            ]
        );

        // 4. सेवाहरू
        Service::firstOrCreate(
            ['name' => 'सवारी चालक अनुमतिपत्र नवीकरण', 'department_id' => $deptTransport->id],
            [
                'description' => 'आफ्नो सवारी चालक अनुमतिपत्र यातायात व्यवस्था कार्यालयमा नगई अनलाइनबाट नवीकरण गर्नुहोस्।',
                'required_documents' => ['अवधि नसकिएको चालक अनुमतिपत्रको प्रतिलिपि', 'बसोबास प्रमाण', 'हालसालैको आँखा जाँच प्रमाणपत्र'],
                'fee' => 1500.00,
                'processing_days' => 5,
                'status' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'सवारी साधन दर्ता तथा नम्बर प्लेट', 'department_id' => $deptTransport->id],
            [
                'description' => 'नयाँ वा हस्तान्तरण गरिएको सवारी साधन दर्ता गर्नुहोस् र आधिकारिक नम्बर प्लेट अनुरोध गर्नुहोस्।',
                'required_documents' => ['सवारी खरिद बिल', 'बिमा प्रमाणपत्र', 'प्रदूषण जाँच प्रमाणपत्र'],
                'fee' => 3500.00,
                'processing_days' => 3,
                'status' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'राहदानी जारी तथा नवीकरण', 'department_id' => $deptImmigration->id],
            [
                'description' => 'नयाँ बायोमेट्रिक राहदानीको लागि आवेदन दिनुहोस् वा म्याद नाघ्न लागेको यात्रा कागजात नवीकरण गर्नुहोस्।',
                'required_documents' => ['नागरिकता प्रमाणपत्र / जन्मदर्ता', 'हालसालैको पासपोर्ट साइजको फोटो', 'नागरिकताको प्रमाण'],
                'fee' => 5000.00,
                'processing_days' => 10,
                'status' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'नागरिकता प्रमाणपत्र प्रमाणीकरण', 'department_id' => $deptImmigration->id],
            [
                'description' => 'नागरिकता कागजातको आधिकारिक प्रमाणीकरण र प्रमाणित प्रतिलिपिको लागि अनुरोध गर्नुहोस्।',
                'required_documents' => ['बाबु-आमाको जन्मदर्ता', 'बसोबास प्रमाण'],
                'fee' => 500.00,
                'processing_days' => 7,
                'status' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'जन्मदर्ता प्रमाणपत्र जारी', 'department_id' => $deptCivil->id],
            [
                'description' => 'कानुनी, शैक्षिक, वा परिचयको उद्देश्यका लागि जन्मदर्ता प्रमाणपत्रको प्रमाणित प्रतिलिपि अर्डर गर्नुहोस्।',
                'required_documents' => ['अस्पतालको जन्म सूचना', 'अभिभावकको नागरिकता प्रतिलिपि'],
                'fee' => 250.00,
                'processing_days' => 2,
                'status' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'विवाह दर्ता प्रमाणपत्र', 'department_id' => $deptCivil->id],
            [
                'description' => 'विवाह दर्ता गर्नुहोस् र आधिकारिक कानुनी विवाह कागजात प्राप्त गर्नुहोस्।',
                'required_documents' => ['दुवै पक्षको नागरिकता प्रमाणपत्र', 'साक्षी परिचय फारम', 'सम्बन्धविच्छेद आदेश (पूर्व विवाहित भए)'],
                'fee' => 1000.00,
                'processing_days' => 4,
                'status' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'आवासीय भवन निर्माण अनुमति', 'department_id' => $deptHousing->id],
            [
                'description' => 'आवासीय सम्पत्तिको संरचनात्मक निर्माण, नवीकरण, वा विस्तारको लागि अनुमति प्राप्त गर्नुहोस्।',
                'required_documents' => ['वास्तुकला नक्सा (ब्लुप्रिन्ट)', 'जग्गाधनीपुर्जा', 'वातावरणीय प्रभाव मूल्याङ्कन'],
                'fee' => 4500.00,
                'processing_days' => 14,
                'status' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'व्यावसायिक व्यवसाय इजाजत दर्ता', 'department_id' => $deptBusiness->id],
            [
                'description' => 'नयाँ साना व्यवसाय, साझेदारी, वा कम्पनी दर्ता गर्नुहोस् र आधिकारिक परिचालन अनुमति प्राप्त गर्नुहोस्।',
                'required_documents' => ['व्यवसाय दर्ताको लागि निवेदन', 'प्यान / कर नम्बर', 'कार्यालय भाडा वा स्वामित्व कागजात'],
                'fee' => 2500.00,
                'processing_days' => 7,
                'status' => true,
            ]
        );

        // 5. सार्वजनिक सूचनाहरू
        Notice::firstOrCreate(
            ['title' => 'पोर्टल रखरखाव तथा प्रणाली स्तरोन्नति सूचना'],
            [
                'content' => 'अनलाइन सरकारी सेवा पोर्टल आइतबार राति २:०० बजेदेखि बिहान ६:०० बजेसम्म अनुसूचित रखरखावको लागि बन्द रहनेछ। यस अवधिमा अनलाइन निवेदन सेवा अस्थायी रूपमा उपलब्ध नहुन सक्छ।',
                'published_at' => now()->subDays(1),
                'is_active' => true,
            ]
        );

        Notice::firstOrCreate(
            ['title' => 'द्रुत राहदानी तथा कागजात वितरण सेवा शुरू'],
            [
                'content' => 'नागरिकहरूले अब नवीकरण गरिएको राहदानी र प्रमाणित कागजातहरू आफ्नो दर्ता ठेगानामा एक्सप्रेस कुरियरद्वारा प्राप्त गर्न सक्नुहुनेछ।',
                'published_at' => now()->subDays(3),
                'is_active' => true,
            ]
        );

        Notice::firstOrCreate(
            ['title' => 'डिजिटल दस्तुर भुक्तानी प्रणाली विस्तार'],
            [
                'content' => 'अब हामी eSewa, Khalti, ConnectIPS, बैंक ट्रान्सफर, र सबै प्रमुख क्रेडिट/डेबिट कार्डबाट तत्काल अनलाइन भुक्तानी स्वीकार गर्दछौं।',
                'published_at' => now()->subDays(5),
                'is_active' => true,
            ]
        );
    }
}