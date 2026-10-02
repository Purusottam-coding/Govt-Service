<?php

use App\Models\Department;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $codeMap = [
            1 => 'YAT', // यातायात व्यवस्था विभाग
            2 => 'PAS', // राहदानी विभाग
            3 => 'NAG', // नागरिक दर्ता विभाग
            4 => 'BHA', // नगरपालिका तथा भवन निर्माण
            5 => 'BAN', // वाणिज्य तथा आपूर्ति
            6 => 'KAB', // बाह्रदशी
        ];

        foreach ($codeMap as $id => $code) {
            Department::where('id', $id)->update(['code' => $code]);
        }

        // Also ensure key branches from workflow sketch exist
        Department::firstOrCreate(
            ['name' => 'योजना तथा पूर्वाधार विकास शाखा'],
            [
                'code' => 'YOJ',
                'description' => 'गाउँपालिकाका सडक, खानेपानी, पुल, सिँचाइ तथा पूर्वाधार योजनाहरू',
                'phone' => '०२३-५८०००१',
                'email' => 'yojana@barhadashi.gov.np',
                'status' => true,
            ]
        );

        Department::firstOrCreate(
            ['name' => 'राजस्व तथा आर्थिक प्रशासन शाखा'],
            [
                'code' => 'RAJ',
                'description' => 'सम्पत्ति कर, व्यवसाय कर, सिफारिस दस्तुर तथा आन्तरिक राजस्व संकलन',
                'phone' => '०२३-५८०००२',
                'email' => 'rajaswa@barhadashi.gov.np',
                'status' => true,
            ]
        );

        Department::firstOrCreate(
            ['name' => 'शिक्षा, युवा तथा खेलकुद शाखा'],
            [
                'code' => 'SIK',
                'description' => 'सामुदायिक विद्यालय अनुगमन, छात्रवृत्ति वितरण तथा खेलकुद प्रतियोगिता',
                'phone' => '०२३-५८०००३',
                'email' => 'shiksha@barhadashi.gov.np',
                'status' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
