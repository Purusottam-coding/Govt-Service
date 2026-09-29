<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Service;
use App\Models\Application;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of branches (departments) with their yojanas/services.
     */
    public function index(Request $request)
    {
        $query = Department::withCount('services')->where('status', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $departments = $query->get();

        return view('citizen.branches.index', compact('departments'));
    }

    /**
     * Display specific branch details, yojanas, room info & step-by-step guide.
     */
    public function show(Department $department)
    {
        $department->load([
            'services' => function ($query) {
                $query->where('status', true);
            }
        ]);

        // Specific branch metadata & room assignment mapping
        $branchMeta = $this->getBranchMetadata($department);

        return view('citizen.branches.show', compact('department', 'branchMeta'));
    }

    /**
     * Interactive Citizen Application & Branch Verification Page.
     */
    public function verify(Request $request)
    {
        $application = null;
        $searchQuery = $request->input('query');
        $departmentId = $request->input('department_id');

        if (!empty($searchQuery)) {
            $query = Application::with(['service.department', 'user', 'payment', 'documents']);

            if ($departmentId) {
                $query->whereHas('service', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }

            $application = $query->where(function ($q) use ($searchQuery) {
                $q->where('application_number', trim($searchQuery))
                    ->orWhere('certificate_number', strtoupper(trim($searchQuery)));
            })->first();
        }

        $departments = Department::where('status', true)->get();

        return view('citizen.branches.verify', compact('application', 'searchQuery', 'departments'));
    }

    /**
     * Helper to get structured branch metadata (Room, Officer, Process Steps).
     */
    public function getBranchMetadata(Department $department): array
    {
        $name = $department->name;

        if (str_contains($name, 'यातायात')) {
            return [
                'code' => 'YATAYAT-104',
                'room' => 'कोठा नं. १०४, पहिलो तल्ला',
                'officer' => 'शाखा प्रमुख: श्री रमेश अधिकारी (इन्जिनियर)',
                'phone' => $department->phone ?? '०१-४२११५४०',
                'email' => $department->email ?? 'transport@gov.np',
                'hours' => '१०:०० AM - ०५:०० PM (आइत-बिही), १०:०० AM - ०३:०० PM (शुक्र)',
                'steps' => [
                    ['step' => 1, 'title' => 'अनलाइन निवेदन', 'desc' => 'यस प्रणालीबाट सवारी चालक अनुमतिपत्र नवीकरण वा नयाँ दर्ता निवेदन फारम भर्नुहोस्।'],
                    ['step' => 2, 'title' => 'शाखा रुजु (कोठा १०४)', 'desc' => 'सम्बन्धित शाखामा कागजात, आँखा जाँच तथा बायोमेट्रिक रुजु गराउनुहोस्।'],
                    ['step' => 3, 'title' => 'राजस्व भुक्तानी', 'desc' => 'इ-सेवा, खल्ती वा राजस्व काउन्टर (कोठा १०२) बाट तोकिएको दस्तुर बुझाउनुहोस्।'],
                    ['step' => 4, 'title' => 'लाइसेन्स / परमिट प्राप्ति', 'desc' => 'शाखा प्रमुख स्वीकृति पश्चात डिजिटल प्रमाण पत्र वा स्मार्ट कार्ड प्राप्त गर्नुहोस्।'],
                ]
            ];
        } elseif (str_contains($name, 'राहदानी') || str_contains($name, 'अध्यागमन')) {
            return [
                'code' => 'PASSPORT-203',
                'room' => 'कोठा नं. २०३, दोस्रो तल्ला',
                'officer' => 'शाखा प्रमुख: श्रीमती सुनिता शर्मा (सहायक प्रमुख जिल्ला अधिकारी)',
                'phone' => $department->phone ?? '०१-४४१४३३६',
                'email' => $department->email ?? 'passport@gov.np',
                'hours' => '१०:०० AM - ०४:३० PM (आइत-बिही), १०:०० AM - ०२:३० PM (शुक्र)',
                'steps' => [
                    ['step' => 1, 'title' => 'नागरिकता तथा फारम दर्ता', 'desc' => 'अनलाइन प्रणालीबाट राहदानी आवेदन भर्नुहोस् र नागरिकता प्रतिलिपि अपलोड गर्नुहोस्।'],
                    ['step' => 2, 'title' => 'बायोमेट्रिक संकलन (कोठा २०३)', 'desc' => 'राहदानी विभागमा उपस्थित भई फिंगरप्रिन्ट र डिजिटल फोटो दिनुहोस्।'],
                    ['step' => 3, 'title' => 'दस्तुर चुक्ता', 'desc' => 'सम्बन्धित बैंकिङ काउन्टर वा इ-पेमेन्टबाट दस्तुर बुझाउनुहोस्।'],
                    ['step' => 4, 'title' => 'राहदानी हस्तान्तरण', 'desc' => 'तयार भएको बायोमेट्रिक राहदानी शाखा वा एक्सप्रेस कुरियरबाट प्राप्त गर्नुहोस्।'],
                ]
            ];
        } elseif (str_contains($name, 'दर्ता') || str_contains($name, 'पञ्जीकरण')) {
            return [
                'code' => 'CIVIL-101',
                'room' => 'कोठा नं. १०१, भुइँ तल्ला (नागरिक सहायता कक्ष)',
                'officer' => 'पञ्जीकरण अधिकारी: श्री हरिकृष्ण सुवेदी',
                'phone' => $department->phone ?? '०१-४२११७८३',
                'email' => $department->email ?? 'civilreg@gov.np',
                'hours' => '१०:०० AM - ०५:०० PM (आइत-बिही)',
                'steps' => [
                    ['step' => 1, 'title' => 'घटना विवरण फारम', 'desc' => 'जन्म, विवाह वा मृत्यु दर्ता फारम आवश्यक कागजातसहित अनलाइन प्रविष्ट गर्नुहोस्।'],
                    ['step' => 2, 'title' => 'वडा / शाखा प्रमाणीकरण', 'desc' => 'वडा कार्यालय वा दर्ता शाखाबाट सूचना रुजु गराउनुहोस्।'],
                    ['step' => 3, 'title' => 'न्यूनतम दस्तुर बुझाउने', 'desc' => 'अनलाइन वा काउन्टरबाट तोकिएको दस्तुर चुक्ता गर्नुहोस्।'],
                    ['step' => 4, 'title' => 'प्रमाणपत्र प्राप्त', 'desc' => 'डिजिटल क्युआर सहितको आधिकारिक दर्ता प्रमाणपत्र डाउनलोड वा प्राप्त गर्नुहोस्।'],
                ]
            ];
        } elseif (str_contains($name, 'भवन') || str_contains($name, 'आवास') || str_contains($name, 'पूर्वाधार')) {
            return [
                'code' => 'HOUSING-302',
                'room' => 'कोठा नं. ३०२, तेस्रो तल्ला (इन्जिनियरिङ शाखा)',
                'officer' => 'प्रमुख इन्जिनियर: ई. प्रकाश देवकोटा',
                'phone' => $department->phone ?? '०१-४२११४५१',
                'email' => $department->email ?? 'housing@gov.np',
                'hours' => '१०:०० AM - ०५:०० PM (आइत-बिही)',
                'steps' => [
                    ['step' => 1, 'title' => 'नक्सा तथा ब्लुप्रिन्ट पेश', 'desc' => 'घर निर्माण वा जग्गा सम्बन्धी नक्सा र लालपुर्जा अनलाइन अपलोड गर्नुहोस्।'],
                    ['step' => 2, 'title' => 'स्थलगत अनुगमन र प्रतिवेदन', 'desc' => 'इन्जिनियर टोलीद्वारा निर्माण स्थल निरीक्षण र प्रतिवेदन तयारी।'],
                    ['step' => 3, 'title' => 'नक्सा पास दस्तुर', 'desc' => 'सम्पत्ति तथा निर्माण क्षेत्रफल अनुसारको नक्सा पास दस्तुर बुझाउने।'],
                    ['step' => 4, 'title' => 'स्वीकृति पत्र जारी', 'desc' => 'स्थायी / अस्थायी भवन निर्माण स्वीकृति पत्र प्राप्त गर्नुहोस्।'],
                ]
            ];
        } else {
            return [
                'code' => 'GEN-' . $department->id,
                'room' => 'कोठा नं. १०५, पहिलो तल्ला',
                'officer' => 'शाखा प्रमुख: प्रशासन अधिकारी',
                'phone' => $department->phone ?? '०२३-५८०१११',
                'email' => $department->email ?? 'info@bahradashimun.gov.np',
                'hours' => '१०:०० AM - ०५:०० PM (आइत-बिही), १०:०० AM - ०३:०० PM (शुक्र)',
                'steps' => [
                    ['step' => 1, 'title' => 'अनलाइन फारम भर्ने', 'desc' => 'योजना/सेवा छनोट गरी आवश्यक कागजात पेश गर्नुहोस्।'],
                    ['step' => 2, 'title' => 'शाखामा कागजात रुजु', 'desc' => 'सम्बन्धित शाखाबाट कागजात प्रमाणीकरण गराउनुहोस्।'],
                    ['step' => 3, 'title' => 'दस्तुर चुक्ता', 'desc' => 'अनलाइन वा काउन्टरबाट राजस्व दस्तुर बुझाउनुहोस्।'],
                    ['step' => 4, 'title' => 'नतिजा / प्रमाण पत्र प्राप्ति', 'desc' => 'स्वीकृत कागजात डाउनलोड वा शाखाबाट संकलन गर्नुहोस्।'],
                ]
            ];
        }
    }
}
