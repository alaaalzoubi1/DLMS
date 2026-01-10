<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabBridge | نظام إدارة معامل الأسنان المتكامل</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> <!-- ربط ملف الـ CSS -->
</head>

<body>
    <!-- SCRIPT (لا تقم بتغييره) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1
            });
            const hiddenElements = document.querySelectorAll('.hidden-anim');
            hiddenElements.forEach(el => observer.observe(el));

            const allFaqItems = document.querySelectorAll('.faq-item');
            allFaqItems.forEach(item => {
                item.addEventListener('toggle', (event) => {
                    if (item.open) {
                        allFaqItems.forEach(otherItem => {
                            if (otherItem !== item && otherItem.open) {
                                otherItem.removeAttribute('open');
                            }
                        });
                    }
                });
            });
        });
    </script>

    <header>
        <div class="container">
            <a href="#" class="logo">
                <img src="{{ asset('images/logowithname_outnew.png') }}" alt="LabBridge Logo" class="logo-img"> <!-- ربط الصورة -->
            </a>
            <nav>
                <ul>
                    <li><a href="#features">المميزات</a></li>
                    <li><a href="#workflow">دورة العمل</a></li>
                    <li><a href="#platforms">المنصات</a></li>
                    <li><a href="#pricing">الأسعار</a></li>
                </ul>
            </nav>
            <div class="header-cta">
                <a href="#pricing" class="btn-primary">ابدأ 14 يوم مجاناً</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-grid">
                    <div class="hero-content">
                        <h1>الجسر الرقمي الذي يربط <br><span>معمل الأسنان بعيادة الأسنان</span></h1>
                        <p>وداعًا للفوضى، التأخير، وضياع الحالات. نظام واحد يربط الطبيب، الفني، المندوب والمحاسب لإدارة الطلبات، التتبع، والفوترة بوضوح كامل.</p>
                        <div class="hero-btns">
                            <a href="#pricing" class="btn-primary">اشترك الآن</a>
                            <a href="#workflow" class="btn-secondary">كيف يعمل؟</a>
                        </div>
                    </div>
                    <div class="hero-mockup">
                        <img src="{{ asset('images/mockup.png') }}" alt="تطبيق LabBridge على الاجهزة" class="mockup-laptop"> <!-- ربط الصورة -->
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features">
            <div class="container">
                <h2 class="section-title">وداعًا للمكالمات والواتساب… كل شغل المعمل في مكان واحد</h2>
                <div class="features-grid">
                    <!-- Feature Cards Here -->
                    <div class="feature-card hidden-anim">
                        <i class="fas fa-notes-medical"></i>
                        <h3>الطلب يوصل بدون مكالمة</h3>
                        <p>الطبيب يطلب مباشرة من التطبيق بكل التفاصيل، بدون اتصالات، بدون رسائل، وبدون نسيان.</p>
                    </div>
                    <div class="feature-card hidden-anim">
                        <i class="fas fa-qrcode"></i>
                        <h3>كل حالة لها QR</h3>
                        <p>رمز QR فريد يُطبع على الطبعة، مسح واحد ويطلع اسم المريض، رقم الحالة، وملف المريض.</p>
                    </div>
                    <div class="feature-card hidden-anim">
                        <i class="fas fa-users-cog"></i>
                        <h3>كل فني يعرف شغله</h3>
                        <p>توزيع الحالات حسب التخصص (زيركون، كاد كام، شمع…) والحالة تمشي مرحلة مرحلة بوضوح.</p>
                    </div>
                    <div class="feature-card hidden-anim">
                        <i class="fas fa-bell"></i>
                        <h3>إشعارات بدون ما تسأل</h3>
                        <p>إشعارات تلقائية للطبيب، المعمل، والمندوب بكل تحديث… بدون “وين وصل الأوردر؟”.</p>
                    </div>
                    <div class="feature-card hidden-anim">
                        <i class="fas fa-tags"></i>
                        <h3>سعر مختلف لكل طبيب</h3>
                        <p>حدد أسعار خاصة لكل طبيب أو عيادة بسهولة، حسب شغلك واتفاقك معهم.</p>
                    </div>
                    <div class="feature-card hidden-anim">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <h3>فواتير بدون صداع</h3>
                        <p>إصدار فواتير، إضافة دفعات، وتقارير متوافقة مع متطلبات هيئة الزكاة والضريبة.</p>
                    </div>
                    <div class="feature-card hidden-anim">
                        <i class="fas fa-file-upload"></i>
                        <h3>رفع ملفات STL للعمل عليها</h3>
                        <p>الطبيب يمكنه رفع ملفات STL مباشرة من العيادة، والفنيون في المعمل يقومون بالعمل عليها بكل دقة.</p>
                    </div>
                    <div class="feature-card hidden-anim">
                        <i class="fas fa-shield-alt"></i>
                        <h3>كل واحد بصلاحياته</h3>
                        <p>تحكم كامل في صلاحيات كل مستخدم. كل واحد يشوف اللي يخصه فقط لضمان سرية البيانات.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="social-proof">
            <div class="container">
                <h2 class="section-title">موثوق به من معامل أسنان حقيقية</h2>
                <div class="stats-grid">
                    <div class="stat-box">
                        <h3>+120</h3>
                        <p>معمل أسنان نشط</p>
                    </div>
                    <div class="stat-box">
                        <h3>+18,000</h3>
                        <p>حالة تم تتبعها عبر النظام</p>
                    </div>
                    <div class="stat-box">
                        <h3>40%</h3>
                        <p>تقليل وقت المتابعة اليومية</p>
                    </div>
                </div>
                <div class="testimonial">
                    <p>“LabBridge غيّر طريقة إدارتنا للطلبات. لا فقدان، لا تأخير، كل شيء واضح من أول لحظة.”</p>
                    <strong>— مدير معمل أسنان، الرياض</strong>
                </div>
            </div>
        </section>

        <section id="workflow" class="workflow">
            <div class="container">
                <h2 class="section-title">كيف تُدار الطلبات في LabBridge؟</h2>
                <div class="workflow-line">
                    <!-- Workflow Steps Here -->
                    <div class="workflow-step">
                        <div class="step-icon-wrapper"><i class="fas fa-notes-medical"></i></div>
                        <h4>1. الطبيب يطلب</h4>
                        <p>إنشاء طلب رقمي كامل من العيادة مع جميع التفاصيل.</p>
                    </div>
                    <div class="workflow-step">
                        <div class="step-icon-wrapper"><i class="fas fa-qrcode"></i></div>
                        <h4>2. طباعة QR Code</h4>
                        <p>رمز QR فريد يُلصق على الطبعة لمنع الضياع.</p>
                    </div>
                    <div class="workflow-step">
                        <div class="step-icon-wrapper"><i class="fas fa-cogs"></i></div>
                        <h4>3. الفني يستلم</h4>
                        <p>الفني المختص يستلم الطلب حسب تخصصه مباشرة.</p>
                    </div>
                    <div class="workflow-step">
                        <div class="step-icon-wrapper"><i class="fas fa-tasks"></i></div>
                        <h4>4. تتبع المراحل</h4>
                        <p>تنتقل الحالة بين الفنيين بوضوح حسب المراحل المحددة.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="platforms" class="platforms-download">
            <div class="container">
                <h2 class="section-title">نظام متوفر على كل المنصات</h2>
                <div class="platform-cards-container">
                    <!-- Download Platforms Cards Here -->
                    <div class="platform-card">
                        <div class="platform-icons"><i class="fab fa-apple"></i></div>
                        <p>متاح على iOS</p>
                        <div class="app-buttons">
                            <a href="#" class="app-btn"><i class="fab fa-app-store-ios"></i><div><strong>تحميل الآن</strong><span>لأجهزة iPhone</span></div></a>
                        </div>
                    </div>
                    <div class="platform-card">
                        <div class="platform-icons"><i class="fab fa-android"></i></div>
                        <p>متاح على Android</p>
                        <div class="app-buttons">
                            <a href="#" class="app-btn"><i class="fab fa-google-play"></i><div><strong>تحميل الآن</strong><span>لأجهزة Android</span></div></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing" class="pricing">
            <div class="container">
                <h2 class="section-title">خطط الأسعار</h2>
                <div class="price-cards-container">
                    <!-- Pricing Cards Here -->
                    <div class="price-card">
                        <h3>الخطة الأساسية</h3>
                        <div class="price">0 <span>ريال سعودي</span></div>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> خدمة مجانية</li>
                            <li><i class="fas fa-check-circle"></i> استخدام محدود</li>
                        </ul>
                        <a href="#" class="btn-primary">اشترك الآن</a>
                    </div>
                    <div class="price-card">
                        <h3>الخطة المتقدمة</h3>
                        <div class="price">150 <span>ريال سعودي</span></div>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> خدمة مميزة</li>
                            <li><i class="fas fa-check-circle"></i> دعم 24/7</li>
                        </ul>
                        <a href="#" class="btn-primary">اشترك الآن</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-links">
            <a href="#">حول</a>
            <a href="#">الشروط والأحكام</a>
            <a href="#">سياسة الخصوصية</a>
        </div>
        <div class="security-note">
            <i class="fas fa-shield-alt"></i> جميع الحقوق محفوظة لشركة LabBridge &copy; 2026
        </div>
    </footer>
</body>

</html>
