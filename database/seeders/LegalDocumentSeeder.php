<?php

namespace Database\Seeders;

use App\Enums\LegalDocumentType;
use App\Models\LegalDocument;
use Illuminate\Database\Seeder;

class LegalDocumentSeeder extends Seeder
{
    public function run(): void
    {
        LegalDocument::updateOrCreate(
            ['type' => LegalDocumentType::PRIVACY_POLICY->value],
            [
                'version' => '1.0',
                'content' => [
                    'en' => $this->privacyPolicyEn(),
                    'ar' => $this->privacyPolicyAr(),
                ],
            ]
        );

        LegalDocument::updateOrCreate(
            ['type' => LegalDocumentType::TERMS_AND_CONDITIONS->value],
            [
                'version' => '1.0',
                'content' => [
                    'en' => $this->termsEn(),
                    'ar' => $this->termsAr(),
                ],
            ]
        );
    }

    private function privacyPolicyEn(): string
    {
        return <<<'EN'
# Privacy Policy for LabBridge

**Last updated:** July 2, 2026

Welcome to LabBridge.

LabBridge respects the privacy of its users and is committed to protecting all personal and business data collected while using the application or website. This policy explains how data is collected, used, and protected.

## 1. Data We Collect

We may collect the following data:

- Account data (name, email, mobile number).
- Facility data (lab name, commercial registration, tax number, national address, contact information).
- Data of users associated with the facility (technicians, accountants, sales representatives, dentists).
- Order data.
- Patient data entered by the user, such as:
  - Patient name.
  - File number.
  - Doctor's notes.
- Work files such as STL files, images, and attachments.
- Billing and payment data.
- Activity logs within the system.
- Device information, operating system, and IP address, for security purposes and service improvement.

## 2. How We Use the Data

We use the data to:

- Operate LabBridge services.
- Manage orders and track manufacturing stages.
- Issue invoices and manage accounts.
- Integrate with the Zakat, Tax and Customs Authority when this service is activated.
- Send notifications to users.
- Improve performance and user experience.
- Provide technical support.
- Protect the system and prevent misuse.

## 3. Patient Files

The doctor or lab is solely responsible for the accuracy of the data entered.

LabBridge does not own any rights to the medical files or STL files belonging to users; this data is used only to provide the service.

## 4. Data Sharing

We do not sell or rent users' data.

Data may only be shared in the following cases:

- With government authorities when there is a legal obligation.
- With cloud and technical service providers necessary to operate the system.
- With the Zakat, Tax and Customs Authority when using the e-invoicing service.
- Based on an official request from competent judicial authorities.

## 5. Data Protection

LabBridge uses appropriate technical and administrative means to protect data, including:

- Encryption of communications.
- Database protection.
- Multiple access permission levels.
- Operation logging.
- Periodic backups.

Despite this, 100% protection cannot be guaranteed.

## 6. Data Retention

Data is retained for the duration of the subscription or as required by applicable regulations.

After subscription termination, some data may be retained for legal, accounting, or security purposes, after which it is deleted or anonymized.

## 7. User Rights

The user has the right to:

- View their data.
- Edit account data.
- Request account deletion in accordance with applicable regulations.
- Request export of their data, if the service is available.

## 8. Cookies

The website may use cookies to improve the user experience and analyze performance.

## 9. Children's Privacy

The service is intended for facilities and adults and does not target children.

## 10. Changes to This Privacy Policy

LabBridge may modify this policy at any time. The updated version will be published within the application or website, and continued use of the service constitutes acceptance of the modifications.

## Contact

For privacy-related inquiries, please contact us through LabBridge's official communication channels.
EN;
    }

    private function privacyPolicyAr(): string
    {
        return <<<'AR'
# سياسة الخصوصية لتطبيق LabBridge

**آخر تحديث:** 2 يوليو 2026

مرحبًا بكم في LabBridge.

تحترم LabBridge خصوصية مستخدميها، وتلتزم بحماية جميع البيانات الشخصية والتجارية التي يتم جمعها أثناء استخدام التطبيق أو الموقع الإلكتروني. توضح هذه السياسة كيفية جمع البيانات واستخدامها وحمايتها.

## أولاً: البيانات التي نجمعها

قد نقوم بجمع البيانات التالية:

- بيانات الحساب (الاسم، البريد الإلكتروني، رقم الجوال).
- بيانات المنشأة (اسم المعمل، السجل التجاري، الرقم الضريبي، العنوان الوطني، معلومات التواصل).
- بيانات المستخدمين المرتبطين بالمنشأة (الفنيين، المحاسبين، المندوبين، أطباء الأسنان).
- بيانات الطلبات.
- بيانات المرضى التي يقوم المستخدم بإدخالها مثل:
  - اسم المريض.
  - رقم الملف.
  - ملاحظات الطبيب.
- ملفات العمل مثل STL والصور والمرفقات.
- بيانات الفواتير والدفعات.
- سجلات النشاط داخل النظام.
- معلومات الجهاز ونظام التشغيل وعنوان IP لأغراض الأمان وتحسين الخدمة.

## ثانياً: كيفية استخدام البيانات

نستخدم البيانات من أجل:

- تشغيل خدمات LabBridge.
- إدارة الطلبات وتتبع مراحل التصنيع.
- إصدار الفواتير وإدارة الحسابات.
- التكامل مع هيئة الزكاة والضريبة والجمارك عند تفعيل هذه الخدمة.
- إرسال الإشعارات للمستخدمين.
- تحسين الأداء وتجربة الاستخدام.
- تقديم الدعم الفني.
- حماية النظام ومنع إساءة الاستخدام.

## ثالثاً: ملفات المرضى

يتحمل الطبيب أو المعمل مسؤولية صحة البيانات التي يتم إدخالها.

لا تمتلك LabBridge أي حقوق على الملفات الطبية أو ملفات STL الخاصة بالمستخدمين، وتستخدم هذه البيانات فقط لتقديم الخدمة.

## رابعاً: مشاركة البيانات

لا نقوم ببيع أو تأجير بيانات المستخدمين.

قد تتم مشاركة البيانات فقط في الحالات التالية:

- مع الجهات الحكومية عند وجود التزام قانوني.
- مع مزودي الخدمات السحابية والتقنية اللازمة لتشغيل النظام.
- مع هيئة الزكاة والضريبة والجمارك عند استخدام خدمة الفوترة الإلكترونية.
- بناءً على طلب رسمي من الجهات القضائية المختصة.

## خامساً: حماية البيانات

تستخدم LabBridge وسائل تقنية وإدارية مناسبة لحماية البيانات، بما في ذلك:

- تشفير الاتصالات.
- حماية قواعد البيانات.
- صلاحيات وصول متعددة.
- تسجيل العمليات.
- النسخ الاحتياطي الدوري.

ورغم ذلك لا يمكن ضمان الحماية بنسبة 100%.

## سادساً: الاحتفاظ بالبيانات

يتم الاحتفاظ بالبيانات طوال فترة الاشتراك أو حسب ما تقتضيه الأنظمة المعمول بها.

بعد إنهاء الاشتراك قد يتم الاحتفاظ ببعض البيانات لأغراض قانونية أو محاسبية أو أمنية، ثم يتم حذفها أو إخفاء هوية أصحابها.

## سابعاً: صلاحيات المستخدم

يحق للمستخدم:

- الاطلاع على بياناته.
- تعديل بيانات الحساب.
- طلب حذف الحساب وفق الأنظمة المعمول بها.
- طلب تصدير بياناته إذا كانت الخدمة متاحة.

## ثامناً: ملفات تعريف الارتباط

قد يستخدم الموقع الإلكتروني ملفات تعريف الارتباط لتحسين تجربة المستخدم وتحليل الأداء.

## تاسعاً: خصوصية الأطفال

الخدمة مخصصة للمنشآت والأشخاص البالغين ولا تستهدف الأطفال.

## عاشراً: تعديل سياسة الخصوصية

يجوز لـ LabBridge تعديل هذه السياسة في أي وقت، ويتم نشر النسخة المحدثة داخل التطبيق أو الموقع، ويعد استمرار استخدام الخدمة موافقة على التعديلات.

## التواصل

للاستفسارات المتعلقة بالخصوصية يمكن التواصل عبر وسائل التواصل الرسمية الخاصة بـ LabBridge.
AR;
    }

    private function termsEn(): string
    {
        return <<<'EN'
# Terms and Conditions of Use for LabBridge

**Last updated:** July 2, 2026

Please read these terms carefully before using LabBridge, as your use of the system constitutes full acceptance of them.

## 1. Definition

LabBridge is an electronic system for managing dental labs, connecting lab owners, dentists, technicians, accountants, and delivery representatives within a single platform.

## 2. Account Creation

The user must provide accurate and up-to-date information.

The user is responsible for maintaining the confidentiality of their login credentials.

Sharing the account with unauthorized persons is prohibited.

## 3. Subscriptions

The system operates on a monthly or annual subscription basis.

A free trial period may be available according to company policy.

Subscription fees are non-refundable after the usage period begins, unless the refund policy states otherwise.

Subscription prices may be modified in the future, with notice given to the user before renewal.

## 4. System Use

The user undertakes to use the system lawfully and not to:

- Upload files that violate regulations.
- Attempt to hack the system.
- Disrupt the service.
- Copy or resell the system.
- Use the system in a way that harms other users.

## 5. Data Responsibility

The user is solely responsible for:

- Patient data.
- Accuracy of orders.
- Invoices.
- Pricing.
- Uploaded files.

LabBridge is not responsible for errors resulting from entered data.

## 6. E-Invoices

When using the integration service with the Zakat, Tax and Customs Authority, the user is responsible for the accuracy of their regulatory data.

LabBridge is not responsible for invoice rejections due to incorrect data.

## 7. User Permissions

The lab owner can assign user permissions such as:

- Doctors.
- Technicians.
- Accountants.
- Representatives.

The account owner is responsible for granting appropriate permissions.

## 8. Intellectual Property

All rights to the system, design, software, logos, and content belong to LabBridge.

No part of the system may be copied or reused without written consent.

## 9. Service Availability

We strive to provide the service around the clock; however, we do not guarantee it will not be interrupted due to maintenance work or circumstances beyond our control.

## 10. Account Suspension

LabBridge may suspend or terminate any account in cases of:

- Violation of these terms.
- Misuse of the system.
- Attempting to hack the service.
- Using the system for illegal activities.

## 11. Limitation of Liability

LabBridge is not liable for:

- Loss of profits.
- Data loss resulting from user error.
- Indirect damages.
- Failures resulting from internet providers, cloud services, or third parties.

## 12. Service Termination

The user may cancel their subscription at any time.

LabBridge may terminate or suspend the service in case of violation of the terms or for legal reasons.

## 13. Amendments

These terms may be modified at any time, and continued use of the system constitutes acceptance of the updated version.

## 14. Governing Law

These terms are subject to the regulations applicable in the Kingdom of Saudi Arabia, and the competent courts within the Kingdom shall have jurisdiction over any dispute, unless the regulations state otherwise.

## 15. Contact

LabBridge's team can be contacted through official communication channels for inquiries or technical support.
EN;
    }

    private function termsAr(): string
    {
        return <<<'AR'
# شروط وأحكام استخدام LabBridge

**آخر تحديث:** 2 يوليو 2026

يُرجى قراءة هذه الشروط بعناية قبل استخدام LabBridge، حيث يعد استخدامك للنظام موافقة كاملة عليها.

## 1. التعريف

LabBridge هو نظام إلكتروني لإدارة معامل الأسنان وربط أصحاب المعامل وأطباء الأسنان والفنيين والمحاسبين ومندوبي التوصيل ضمن منصة واحدة.

## 2. إنشاء الحساب

يلتزم المستخدم بتقديم معلومات صحيحة ومحدثة.

المستخدم مسؤول عن المحافظة على سرية بيانات تسجيل الدخول.

يمنع مشاركة الحساب مع أشخاص غير مخولين.

## 3. الاشتراكات

يعمل النظام باشتراك شهري أو سنوي.

قد تتوفر فترة تجريبية مجانية حسب سياسة الشركة.

لا يتم استرداد قيمة الاشتراك بعد بدء فترة الاستخدام إلا إذا نصت سياسة الاسترداد على خلاف ذلك.

يجوز تعديل أسعار الاشتراك مستقبلاً مع إشعار المستخدم قبل التجديد.

## 4. استخدام النظام

يتعهد المستخدم باستخدام النظام استخداماً مشروعاً وعدم:

- رفع ملفات مخالفة للأنظمة.
- محاولة اختراق النظام.
- تعطيل الخدمة.
- نسخ النظام أو إعادة بيعه.
- استخدام النظام بطريقة تضر بالمستخدمين الآخرين.

## 5. مسؤولية البيانات

يتحمل المستخدم وحده مسؤولية:

- بيانات المرضى.
- صحة الطلبات.
- الفواتير.
- الأسعار.
- الملفات المرفوعة.

ولا تتحمل LabBridge مسؤولية الأخطاء الناتجة عن البيانات المدخلة.

## 6. الفواتير الإلكترونية

عند استخدام خدمة الربط مع هيئة الزكاة والضريبة والجمارك، فإن المستخدم مسؤول عن صحة بياناته النظامية.

ولا تتحمل LabBridge مسؤولية رفض الفواتير بسبب بيانات غير صحيحة.

## 7. صلاحيات المستخدمين

يمكن لصاحب المعمل تحديد صلاحيات المستخدمين مثل:

- الأطباء.
- الفنيين.
- المحاسبين.
- المندوبين.

ويتحمل صاحب الحساب مسؤولية منح الصلاحيات المناسبة.

## 8. الملكية الفكرية

جميع حقوق النظام والتصميم والبرمجيات والشعارات والمحتوى تعود إلى LabBridge.

ولا يجوز نسخ أي جزء من النظام أو إعادة استخدامه دون موافقة كتابية.

## 9. إتاحة الخدمة

نسعى لتوفير الخدمة على مدار الساعة، إلا أننا لا نضمن عدم انقطاعها نتيجة أعمال الصيانة أو الظروف الخارجة عن الإرادة.

## 10. إيقاف الحساب

يجوز لـ LabBridge تعليق أو إيقاف أي حساب عند:

- مخالفة هذه الشروط.
- إساءة استخدام النظام.
- محاولة اختراق الخدمة.
- استخدام النظام في أعمال غير قانونية.

## 11. تحديد المسؤولية

لا تتحمل LabBridge أي مسؤولية عن:

- فقدان الأرباح.
- فقدان البيانات الناتج عن خطأ المستخدم.
- الأضرار غير المباشرة.
- الأعطال الناتجة عن مزودي الإنترنت أو الخدمات السحابية أو الجهات الخارجية.

## 12. إنهاء الخدمة

يجوز للمستخدم إلغاء اشتراكه في أي وقت.

ويجوز لـ LabBridge إنهاء الخدمة أو إيقافها عند مخالفة الشروط أو عند وجود أسباب قانونية.

## 13. التعديلات

يجوز تعديل هذه الشروط في أي وقت، ويعد استمرار استخدام النظام موافقة على النسخة المحدثة.

## 14. القانون الواجب التطبيق

تخضع هذه الشروط للأنظمة المعمول بها في المملكة العربية السعودية، وتكون المحاكم المختصة داخل المملكة هي الجهة المختصة بالنظر في أي نزاع، ما لم تنص الأنظمة على خلاف ذلك.

## 15. التواصل

يمكن التواصل مع فريق LabBridge عبر وسائل التواصل الرسمية للاستفسارات أو الدعم الفني.
AR;
    }
}
