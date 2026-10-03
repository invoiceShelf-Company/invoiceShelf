<?php

namespace App\Support;

class LocalizedText
{
    public static function get(string $text): string
    {
        static $arabicKeys;

        $arabicKeys ??= self::loadArabicKeys();
        $key = $arabicKeys[$text] ?? null;

        if ($key) {
            return __($key);
        }

        $fallback = self::fallbacks()[$text] ?? null;

        return $fallback[app()->getLocale()] ?? $text;
    }

    public static function translateRendered(string $content): string
    {
        if (app()->getLocale() === 'ar') {
            return $content;
        }

        $translations = self::fallbacks();
        $arabicKeys = self::loadArabicKeys();
        $localeTranslations = self::loadLocaleTranslations(app()->getLocale());

        foreach ($arabicKeys as $arabic => $key) {
            if (isset($localeTranslations[$key]) && $localeTranslations[$key] !== $arabic) {
                $translations[$arabic] = [app()->getLocale() => $localeTranslations[$key]];
            }
        }

        $replacements = [];
        foreach ($translations as $arabic => $languages) {
            if (isset($languages[app()->getLocale()]) && $languages[app()->getLocale()] !== $arabic) {
                $replacements[$arabic] = $languages[app()->getLocale()];
            }
        }

        uksort($replacements, static fn (string $left, string $right): int => mb_strlen($right) <=> mb_strlen($left));

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    private static function loadLocaleTranslations(string $locale): array
    {
        $path = resource_path("lang/{$locale}.json");

        if (! is_file($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    private static function fallbacks(): array
    {
        return [
            'الرئيسية' => ['en' => 'Main', 'fr' => 'Accueil', 'es' => 'Principal', 'de' => 'Startseite', 'tr' => 'Ana Sayfa', 'zh_CN' => '主页', 'nl_BE' => 'Home'],
            'لوحة التحكم' => ['en' => 'Dashboard', 'fr' => 'Tableau de bord', 'es' => 'Panel', 'de' => 'Dashboard', 'tr' => 'Kontrol Paneli', 'zh_CN' => '仪表板', 'nl_BE' => 'Dashboard'],
            'المنشآت والعقارات' => ['en' => 'Facilities & Properties', 'fr' => 'Installations et propriétés', 'es' => 'Instalaciones y propiedades', 'de' => 'Einrichtungen und Immobilien', 'tr' => 'Tesisler ve Mülkler', 'zh_CN' => '设施和物业', 'nl_BE' => 'Faciliteiten en eigendommen'],
            'الأشخاص والموظفون' => ['en' => 'People & Employees', 'fr' => 'Personnes et employés', 'es' => 'Personas y empleados', 'de' => 'Personen und Mitarbeiter', 'tr' => 'Kişiler ve Çalışanlar', 'zh_CN' => '人员和员工', 'nl_BE' => 'Personen en werknemers'],
            'الأقسام' => ['en' => 'Departments', 'fr' => 'Départements', 'es' => 'Departamentos', 'de' => 'Abteilungen', 'tr' => 'Departmanlar', 'zh_CN' => '部门', 'nl_BE' => 'Afdelingen'],
            'الورديات وساعات الدوام' => ['en' => 'Shifts & Working Hours', 'fr' => 'Équipes et horaires', 'es' => 'Turnos y horarios', 'de' => 'Schichten und Arbeitszeiten', 'tr' => 'Vardiyalar ve Çalışma Saatleri', 'zh_CN' => '班次和工作时间', 'nl_BE' => 'Ploegen en werktijden'],
            'الحضور والأمن' => ['en' => 'Attendance & Security', 'fr' => 'Présence et sécurité', 'es' => 'Asistencia y seguridad', 'de' => 'Anwesenheit und Sicherheit', 'tr' => 'Devam ve Güvenlik', 'zh_CN' => '考勤和安全', 'nl_BE' => 'Aanwezigheid en beveiliging'],
            'الحضور والغياب' => ['en' => 'Attendance', 'fr' => 'Présence', 'es' => 'Asistencia', 'de' => 'Anwesenheit', 'tr' => 'Devam', 'zh_CN' => '考勤', 'nl_BE' => 'Aanwezigheid'],
            'الزوار' => ['en' => 'Visitors', 'fr' => 'Visiteurs', 'es' => 'Visitantes', 'de' => 'Besucher', 'tr' => 'Ziyaretçiler', 'zh_CN' => '访客', 'nl_BE' => 'Bezoekers'],
            'الدخول والخروج' => ['en' => 'Access Logs', 'fr' => 'Journaux d’accès', 'es' => 'Registros de acceso', 'de' => 'Zugangsprotokolle', 'tr' => 'Giriş Kayıtları', 'zh_CN' => '出入记录', 'nl_BE' => 'Toegangslogboeken'],
            'المخزون' => ['en' => 'Inventory', 'fr' => 'Inventaire', 'es' => 'Inventario', 'de' => 'Inventar', 'tr' => 'Stok', 'zh_CN' => '库存', 'nl_BE' => 'Voorraad'],
            'المنتجات' => ['en' => 'Products', 'fr' => 'Produits', 'es' => 'Produits', 'de' => 'Produkte', 'tr' => 'Ürünler', 'zh_CN' => '产品', 'nl_BE' => 'Producten'],
            'التصنيفات' => ['en' => 'Categories', 'fr' => 'Catégories', 'es' => 'Categorías', 'de' => 'Kategorien', 'tr' => 'Kategoriler', 'zh_CN' => '分类', 'nl_BE' => 'Categorieën'],
            'الموردون' => ['en' => 'Suppliers', 'fr' => 'Fournisseurs', 'es' => 'Proveedores', 'de' => 'Lieferanten', 'tr' => 'Tedarikçiler', 'zh_CN' => '供应商', 'nl_BE' => 'Leveranciers'],
            'المستودعات' => ['en' => 'Warehouses', 'fr' => 'Entrepôts', 'es' => 'Almacenes', 'de' => 'Lager', 'tr' => 'Depolar', 'zh_CN' => '仓库', 'nl_BE' => 'Magazijnen'],
            'حركة المخزون' => ['en' => 'Stock Movements', 'fr' => 'Mouvements de stock', 'es' => 'Movimientos de stock', 'de' => 'Lagerbewegungen', 'tr' => 'Stok Hareketleri', 'zh_CN' => '库存变动', 'nl_BE' => 'Voorraadbewegingen'],
            'تسجيل الدخول' => ['en' => 'Log in', 'fr' => 'Connexion', 'es' => 'Iniciar sesión', 'de' => 'Anmelden', 'tr' => 'Giriş yap', 'zh_CN' => '登录', 'nl_BE' => 'Inloggen'],
            'البريد الإلكتروني' => ['en' => 'Email address', 'fr' => 'Adresse e-mail', 'es' => 'Correo electrónico', 'de' => 'E-Mail-Adresse', 'tr' => 'E-posta adresi', 'zh_CN' => '电子邮箱', 'nl_BE' => 'E-mailadres'],
            'كلمة المرور' => ['en' => 'Password', 'fr' => 'Mot de passe', 'es' => 'Contraseña', 'de' => 'Passwort', 'tr' => 'Şifre', 'zh_CN' => '密码', 'nl_BE' => 'Wachtwoord'],
            'تذكرني' => ['en' => 'Remember me', 'fr' => 'Se souvenir de moi', 'es' => 'Recuérdame', 'de' => 'Angemeldet bleiben', 'tr' => 'Beni hatırla', 'zh_CN' => '记住我', 'nl_BE' => 'Onthoud mij'],
            'تسجيل الخروج' => ['en' => 'Log out', 'fr' => 'Déconnexion', 'es' => 'Cerrar sesión', 'de' => 'Abmelden', 'tr' => 'Çıkış yap', 'zh_CN' => '退出登录', 'nl_BE' => 'Uitloggen'],
            'سجّل الدخول لإدارة مخزونك بكفاءة' => ['en' => 'Log in to manage your inventory efficiently', 'fr' => 'Connectez-vous pour gérer votre inventaire efficacement', 'es' => 'Inicia sesión para gestionar tu inventario de forma eficiente', 'de' => 'Melden Sie sich an, um Ihr Inventar effizient zu verwalten', 'tr' => 'Envanterinizi verimli yönetmek için giriş yapın', 'zh_CN' => '登录以高效管理库存', 'nl_BE' => 'Log in om uw voorraad efficiënt te beheren'],
            'وصول آمن ومشفر' => ['en' => 'Secure and encrypted access', 'fr' => 'Accès sécurisé et chiffré', 'es' => 'Acceso seguro y cifrado', 'de' => 'Sicherer und verschlüsselter Zugriff', 'tr' => 'Güvenli ve şifreli erişim', 'zh_CN' => '安全加密访问', 'nl_BE' => 'Veilige en versleutelde toegang'],
            'ليس لديك حساب؟' => ['en' => "Don't have an account?", 'fr' => 'Vous n’avez pas de compte ?', 'es' => '¿No tienes una cuenta?', 'de' => 'Noch kein Konto?', 'tr' => 'Hesabınız yok mu?', 'zh_CN' => '还没有账户？', 'nl_BE' => 'Nog geen account?'],
            'إنشاء حساب جديد' => ['en' => 'Create a new account', 'fr' => 'Créer un compte', 'es' => 'Crear una cuenta', 'de' => 'Neues Konto erstellen', 'tr' => 'Yeni hesap oluştur', 'zh_CN' => '创建新账户', 'nl_BE' => 'Nieuw account maken'],
            'إضافة شخص' => ['en' => 'Add person', 'fr' => 'Ajouter une personne', 'es' => 'Añadir persona', 'de' => 'Person hinzufügen', 'tr' => 'Kişi ekle', 'zh_CN' => '添加人员', 'nl_BE' => 'Persoon toevoegen'],
            'تسجيل حضور' => ['en' => 'Record attendance', 'fr' => 'Enregistrer la présence', 'es' => 'Registrar asistencia', 'de' => 'Anwesenheit erfassen', 'tr' => 'Devam kaydı', 'zh_CN' => '记录考勤', 'nl_BE' => 'Aanwezigheid registreren'],
            'لوحة الإدارة الذكية' => ['en' => 'Smart administration dashboard', 'fr' => 'Tableau de gestion intelligent', 'es' => 'Panel de administración inteligente', 'de' => 'Intelligentes Verwaltungsdashboard', 'tr' => 'Akıllı yönetim paneli', 'zh_CN' => '智能管理面板', 'nl_BE' => 'Slim beheerdashboard'],
            'إدارة المخزون والمنشأة والأشخاص من مكان واحد.' => ['en' => 'Manage inventory, facilities, and people from one place.', 'fr' => 'Gérez les stocks, les installations et les personnes depuis un seul endroit.', 'es' => 'Gestiona inventario, instalaciones y personas desde un solo lugar.', 'de' => 'Verwalten Sie Inventar, Einrichtungen und Personen an einem Ort.', 'tr' => 'Stokları, tesisleri ve kişileri tek yerden yönetin.', 'zh_CN' => '从一个地方管理库存、设施和人员。', 'nl_BE' => 'Beheer voorraad, faciliteiten en personen vanuit één plek.'],
            'الأشخاص النشطون' => ['en' => 'Active people', 'fr' => 'Personnes actives', 'es' => 'Personas activas', 'de' => 'Aktive Personen', 'tr' => 'Aktif kişiler', 'zh_CN' => '活跃人员', 'nl_BE' => 'Actieve personen'],
            'حراس الأمن' => ['en' => 'Security guards', 'fr' => 'Agents de sécurité', 'es' => 'Guardias de seguridad', 'de' => 'Sicherheitskräfte', 'tr' => 'Güvenlik görevlileri', 'zh_CN' => '保安人员', 'nl_BE' => 'Beveiligers'],
            'الحاضرون اليوم' => ['en' => 'Present today', 'fr' => "Présents aujourd'hui", 'es' => 'Presentes hoy', 'de' => 'Heute anwesend', 'tr' => 'Bugün mevcut', 'zh_CN' => '今日出勤', 'nl_BE' => 'Vandaag aanwezig'],
            'الغياب اليوم' => ['en' => 'Absent today', 'fr' => "Absents aujourd'hui", 'es' => 'Ausentes hoy', 'de' => 'Heute abwesend', 'tr' => 'Bugün absent', 'zh_CN' => '今日缺勤', 'nl_BE' => 'Vandaag afwezig'],
            'الساعات الإضافية' => ['en' => 'Overtime hours', 'fr' => 'Heures supplémentaires', 'es' => 'Horas extra', 'de' => 'Überstunden', 'tr' => 'Fazla mesai saatleri', 'zh_CN' => '加班时间', 'nl_BE' => 'Overuren'],
            'قيمة المخزون' => ['en' => 'Inventory value', 'fr' => 'Valeur du stock', 'es' => 'Valor del inventario', 'de' => 'Inventarwert', 'tr' => 'Stok değeri', 'zh_CN' => '库存价值', 'nl_BE' => 'Voorraadwaarde'],
            'حركة المخزون والحضور' => ['en' => 'Inventory and attendance activity', 'fr' => 'Activité des stocks et présences', 'es' => 'Actividad de inventario y asistencia', 'de' => 'Inventar- und Anwesenheitsaktivität', 'tr' => 'Stok ve devam hareketleri', 'zh_CN' => '库存和考勤活动', 'nl_BE' => 'Voorraad- en aanwezigheidsactiviteit'],
            'آخر 7 أيام' => ['en' => 'Last 7 days', 'fr' => '7 derniers jours', 'es' => 'Últimos 7 días', 'de' => 'Letzte 7 Tage', 'tr' => 'Son 7 gün', 'zh_CN' => '最近7天', 'nl_BE' => 'Laatste 7 dagen'],
            'مباشر' => ['en' => 'Live', 'fr' => 'En direct', 'es' => 'En directo', 'de' => 'Live', 'tr' => 'Canlı', 'zh_CN' => '实时', 'nl_BE' => 'Live'],
            'ملخص مالي' => ['en' => 'Financial summary', 'fr' => 'Résumé financier', 'es' => 'Resumen financiero', 'de' => 'Finanzübersicht', 'tr' => 'Mali özet', 'zh_CN' => '财务摘要', 'nl_BE' => 'Financieel overzicht'],
            'القيمة الحالية للمخزون' => ['en' => 'Current inventory value', 'fr' => 'Valeur actuelle du stock', 'es' => 'Valor actual del inventario', 'de' => 'Aktueller Inventarwert', 'tr' => 'Mevcut stok değeri', 'zh_CN' => '当前库存价值', 'nl_BE' => 'Huidige voorraadwaarde'],
            'آخر حركات المخزون' => ['en' => 'Latest stock movements', 'fr' => 'Derniers mouvements de stock', 'es' => 'Últimos movimientos de stock', 'de' => 'Neueste Lagerbewegungen', 'tr' => 'Son stok hareketleri', 'zh_CN' => '最新库存变动', 'nl_BE' => 'Laatste voorraadbewegingen'],
            'أحدث العمليات' => ['en' => 'Latest operations', 'fr' => 'Dernières opérations', 'es' => 'Últimas operaciones', 'de' => 'Neueste Vorgänge', 'tr' => 'Son işlemler', 'zh_CN' => '最新操作', 'nl_BE' => 'Laatste bewerkingen'],
            'تنبيهات المخزون' => ['en' => 'Inventory alerts', 'fr' => 'Alertes de stock', 'es' => 'Alertas de inventario', 'de' => 'Bestandswarnungen', 'tr' => 'Stok uyarıları', 'zh_CN' => '库存警报', 'nl_BE' => 'Voorraadwaarschuwingen'],
            'تحتاج متابعة' => ['en' => 'Needs attention', 'fr' => 'Nécessite un suivi', 'es' => 'Requiere seguimiento', 'de' => 'Benötigt Aufmerksamkeit', 'tr' => 'Takip gerekli', 'zh_CN' => '需要关注', 'nl_BE' => 'Heeft opvolging nodig'],
            'المخزون بحالة جيدة' => ['en' => 'Inventory is in good condition', 'fr' => 'Le stock est en bon état', 'es' => 'El inventario está en buen estado', 'de' => 'Der Bestand ist in gutem Zustand', 'tr' => 'Stok durumu iyi', 'zh_CN' => '库存状况良好', 'nl_BE' => 'Voorraad is in goede staat'],
            'عرض الكل' => ['en' => 'View all', 'fr' => 'Tout afficher', 'es' => 'Ver todo', 'de' => 'Alle anzeigen', 'tr' => 'Tümünü görüntüle', 'zh_CN' => '查看全部', 'nl_BE' => 'Alles bekijken'],
            'إضافة' => ['en' => 'Add', 'fr' => 'Ajouter', 'es' => 'Añadir', 'de' => 'Hinzufügen', 'tr' => 'Ekle', 'zh_CN' => '添加', 'nl_BE' => 'Toevoegen'],
            'تعديل' => ['en' => 'Edit', 'fr' => 'Modifier', 'es' => 'Editar', 'de' => 'Bearbeiten', 'tr' => 'Düzenle', 'zh_CN' => '编辑', 'nl_BE' => 'Bewerken'],
            'حفظ' => ['en' => 'Save', 'fr' => 'Enregistrer', 'es' => 'Guardar', 'de' => 'Speichern', 'tr' => 'Kaydet', 'zh_CN' => '保存', 'nl_BE' => 'Opslaan'],
            'إلغاء' => ['en' => 'Cancel', 'fr' => 'Annuler', 'es' => 'Cancelar', 'de' => 'Abbrechen', 'tr' => 'İptal', 'zh_CN' => '取消', 'nl_BE' => 'Annuleren'],
            'حذف' => ['en' => 'Delete', 'fr' => 'Supprimer', 'es' => 'Eliminar', 'de' => 'Löschen', 'tr' => 'Sil', 'zh_CN' => '删除', 'nl_BE' => 'Verwijderen'],
            'بحث' => ['en' => 'Search', 'fr' => 'Rechercher', 'es' => 'Buscar', 'de' => 'Suchen', 'tr' => 'Ara', 'zh_CN' => '搜索', 'nl_BE' => 'Zoeken'],
            'التاريخ' => ['en' => 'Date', 'fr' => 'Date', 'es' => 'Fecha', 'de' => 'Datum', 'tr' => 'Tarih', 'zh_CN' => '日期', 'nl_BE' => 'Datum'],
            'الشخص' => ['en' => 'Person', 'fr' => 'Personne', 'es' => 'Persona', 'de' => 'Person', 'tr' => 'Kişi', 'zh_CN' => '人员', 'nl_BE' => 'Persoon'],
            'الحالة' => ['en' => 'Status', 'fr' => 'Statut', 'es' => 'Estado', 'de' => 'Status', 'tr' => 'Durum', 'zh_CN' => '状态', 'nl_BE' => 'Status'],
            'حاضر' => ['en' => 'Present', 'fr' => 'Présent', 'es' => 'Presente', 'de' => 'Anwesend', 'tr' => 'Mevcut', 'zh_CN' => '出勤', 'nl_BE' => 'Aanwezig'],
            'متأخر' => ['en' => 'Late', 'fr' => 'En retard', 'es' => 'Tarde', 'de' => 'Verspätet', 'tr' => 'Geç', 'zh_CN' => '迟到', 'nl_BE' => 'Te laat'],
            'غائب' => ['en' => 'Absent', 'fr' => 'Absent', 'es' => 'Ausente', 'de' => 'Abwesend', 'tr' => 'Devamsız', 'zh_CN' => '缺勤', 'nl_BE' => 'Afwezig'],
            'إجازة' => ['en' => 'Leave', 'fr' => 'Congé', 'es' => 'Permiso', 'de' => 'Urlaub', 'tr' => 'İzin', 'zh_CN' => '休假', 'nl_BE' => 'Verlof'],
            'إضافي' => ['en' => 'Overtime', 'fr' => 'Supplémentaire', 'es' => 'Extra', 'de' => 'Überstunden', 'tr' => 'Fazla mesai', 'zh_CN' => '加班', 'nl_BE' => 'Overtime'],
            'النوع' => ['en' => 'Type', 'fr' => 'Type', 'es' => 'Tipo', 'de' => 'Typ', 'tr' => 'Tür', 'zh_CN' => '类型', 'nl_BE' => 'Type'],
            'المنشأة' => ['en' => 'Facility', 'fr' => 'Installation', 'es' => 'Instalación', 'de' => 'Einrichtung', 'tr' => 'Tesis', 'zh_CN' => '设施', 'nl_BE' => 'Faciliteit'],
            'القسم' => ['en' => 'Department', 'fr' => 'Département', 'es' => 'Departamento', 'de' => 'Abteilung', 'tr' => 'Departman', 'zh_CN' => '部门', 'nl_BE' => 'Afdeling'],
            'الوردية' => ['en' => 'Shift', 'fr' => 'Équipe', 'es' => 'Turno', 'de' => 'Schicht', 'tr' => 'Vardiya', 'zh_CN' => '班次', 'nl_BE' => 'Ploeg'],
            'لا توجد بيانات.' => ['en' => 'No data found.', 'fr' => 'Aucune donnée trouvée.', 'es' => 'No se encontraron datos.', 'de' => 'Keine Daten gefunden.', 'tr' => 'Veri bulunamadı.', 'zh_CN' => '未找到数据。', 'nl_BE' => 'Geen gegevens gevonden.'],
            'لا توجد حركات.' => ['en' => 'No movements found.', 'fr' => 'Aucun mouvement trouvé.', 'es' => 'No se encontraron movimientos.', 'de' => 'Keine Bewegungen gefunden.', 'tr' => 'Hareket bulunamadı.', 'zh_CN' => '未找到变动。', 'nl_BE' => 'Geen bewegingen gevonden.'],
            'انضم إلى Laraventry' => ['en' => 'Join Laraventry', 'fr' => 'Rejoignez Laraventry', 'es' => 'Únete a Laraventry', 'de' => 'Werden Sie Teil von Laraventry', 'tr' => "Laraventry'ye katılın", 'zh_CN' => '加入 Laraventry', 'nl_BE' => 'Word lid van Laraventry'],
            'أنشئ حسابك وابدأ بإدارة المنتجات والمخزون والموردين من مكان واحد.' => ['en' => 'Create your account and manage products, inventory, and suppliers from one place.', 'fr' => 'Créez votre compte et gérez vos produits, stocks et fournisseurs depuis un seul endroit.', 'es' => 'Crea tu cuenta y gestiona productos, inventario y proveedores desde un solo lugar.', 'de' => 'Erstellen Sie Ihr Konto und verwalten Sie Produkte, Inventar und Lieferanten an einem Ort.', 'tr' => 'Hesabınızı oluşturun; ürünleri, stokları ve tedarikçileri tek yerden yönetin.', 'zh_CN' => '创建账户，从一个地方管理产品、库存和供应商。', 'nl_BE' => 'Maak uw account aan en beheer producten, voorraad en leveranciers vanuit één plek.'],
            'لوحة تحكم واضحة وسهلة الاستخدام' => ['en' => 'Clear and easy-to-use dashboard', 'fr' => 'Tableau de bord clair et facile à utiliser', 'es' => 'Panel claro y fácil de usar', 'de' => 'Übersichtliches und einfaches Dashboard', 'tr' => 'Açık ve kullanımı kolay panel', 'zh_CN' => '清晰易用的仪表板', 'nl_BE' => 'Duidelijk en gebruiksvriendelijk dashboard'],
            'إدارة المنتجات والمخزون وحركات المخزون' => ['en' => 'Manage products, inventory, and stock movements', 'fr' => 'Gérez les produits, stocks et mouvements de stock', 'es' => 'Gestiona productos, inventario y movimientos de stock', 'de' => 'Produkte, Inventar und Lagerbewegungen verwalten', 'tr' => 'Ürünleri, stokları ve stok hareketlerini yönetin', 'zh_CN' => '管理产品、库存和库存变动', 'nl_BE' => 'Beheer producten, voorraad en voorraadbewegingen'],
            'حساب آمن مع حماية للطلبات' => ['en' => 'Secure account with request protection', 'fr' => 'Compte sécurisé avec protection des requêtes', 'es' => 'Cuenta segura con protección de solicitudes', 'de' => 'Sicheres Konto mit Anfrageschutz', 'tr' => 'İstek korumalı güvenli hesap', 'zh_CN' => '具有请求保护的安全账户', 'nl_BE' => 'Veilig account met verzoekbescherming'],
            'أدخل بياناتك للبدء' => ['en' => 'Enter your details to get started', 'fr' => 'Saisissez vos informations pour commencer', 'es' => 'Introduce tus datos para comenzar', 'de' => 'Geben Sie Ihre Daten ein, um zu beginnen', 'tr' => 'Başlamak için bilgilerinizi girin', 'zh_CN' => '输入您的信息以开始', 'nl_BE' => 'Voer uw gegevens in om te beginnen'],
            'يرجى مراجعة البيانات' => ['en' => 'Please review the information', 'fr' => 'Veuillez vérifier les informations', 'es' => 'Revisa los datos', 'de' => 'Bitte überprüfen Sie die Angaben', 'tr' => 'Lütfen bilgileri kontrol edin', 'zh_CN' => '请检查信息', 'nl_BE' => 'Controleer de gegevens'],
            'الاسم الكامل' => ['en' => 'Full name', 'fr' => 'Nom complet', 'es' => 'Nombre completo', 'de' => 'Vollständiger Name', 'tr' => 'Ad soyad', 'zh_CN' => '全名', 'nl_BE' => 'Volledige naam'],
            'مثال: أحمد محمد' => ['en' => 'Example: John Smith', 'fr' => 'Exemple : Jean Dupont', 'es' => 'Ejemplo: Juan García', 'de' => 'Beispiel: Max Mustermann', 'tr' => 'Örnek: Ahmet Yılmaz', 'zh_CN' => '示例：张三', 'nl_BE' => 'Voorbeeld: Jan Peeters'],
            'قوة كلمة المرور' => ['en' => 'Password strength', 'fr' => 'Force du mot de passe', 'es' => 'Seguridad de la contraseña', 'de' => 'Passwortstärke', 'tr' => 'Şifre gücü', 'zh_CN' => '密码强度', 'nl_BE' => 'Wachtwoordsterkte'],
            'ضعيفة' => ['en' => 'Weak', 'fr' => 'Faible', 'es' => 'Débil', 'de' => 'Schwach', 'tr' => 'Zayıf', 'zh_CN' => '弱', 'nl_BE' => 'Zwak'],
            'متوسطة' => ['en' => 'Medium', 'fr' => 'Moyenne', 'es' => 'Media', 'de' => 'Mittel', 'tr' => 'Orta', 'zh_CN' => '中等', 'nl_BE' => 'Gemiddeld'],
            'جيدة' => ['en' => 'Good', 'fr' => 'Bonne', 'es' => 'Buena', 'de' => 'Gut', 'tr' => 'İyi', 'zh_CN' => '良好', 'nl_BE' => 'Goed'],
            'قوية' => ['en' => 'Strong', 'fr' => 'Forte', 'es' => 'Fuerte', 'de' => 'Stark', 'tr' => 'Güçlü', 'zh_CN' => '强', 'nl_BE' => 'Sterk'],
            'تأكيد كلمة المرور' => ['en' => 'Confirm password', 'fr' => 'Confirmer le mot de passe', 'es' => 'Confirmar contraseña', 'de' => 'Passwort bestätigen', 'tr' => 'Şifreyi onayla', 'zh_CN' => '确认密码', 'nl_BE' => 'Bevestig wachtwoord'],
            'إنشاء الحساب' => ['en' => 'Create account', 'fr' => 'Créer un compte', 'es' => 'Crear cuenta', 'de' => 'Konto erstellen', 'tr' => 'Hesap oluştur', 'zh_CN' => '创建账户', 'nl_BE' => 'Account maken'],
            'جارٍ إنشاء الحساب...' => ['en' => 'Creating account...', 'fr' => 'Création du compte...', 'es' => 'Creando cuenta...', 'de' => 'Konto wird erstellt...', 'tr' => 'Hesap oluşturuluyor...', 'zh_CN' => '正在创建账户...', 'nl_BE' => 'Account wordt aangemaakt...'],
            'لديك حساب بالفعل؟' => ['en' => 'Already have an account?', 'fr' => 'Vous avez déjà un compte ?', 'es' => '¿Ya tienes una cuenta?', 'de' => 'Sie haben bereits ein Konto?', 'tr' => 'Zaten hesabınız var mı?', 'zh_CN' => '已有账户？', 'nl_BE' => 'Heeft u al een account?'],
            'يرجى إدخال اسم صحيح.' => ['en' => 'Please enter a valid name.', 'fr' => 'Veuillez saisir un nom valide.', 'es' => 'Introduce un nombre válido.', 'de' => 'Bitte geben Sie einen gültigen Namen ein.', 'tr' => 'Lütfen geçerli bir ad girin.', 'zh_CN' => '请输入有效姓名。', 'nl_BE' => 'Voer een geldige naam in.'],
            'يرجى إدخال بريد إلكتروني صحيح.' => ['en' => 'Please enter a valid email address.', 'fr' => 'Veuillez saisir une adresse e-mail valide.', 'es' => 'Introduce un correo válido.', 'de' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.', 'tr' => 'Lütfen geçerli bir e-posta girin.', 'zh_CN' => '请输入有效的电子邮箱。', 'nl_BE' => 'Voer een geldig e-mailadres in.'],
            '8 أحرف على الأقل' => ['en' => 'At least 8 characters', 'fr' => 'Au moins 8 caractères', 'es' => 'Al menos 8 caracteres', 'de' => 'Mindestens 8 Zeichen', 'tr' => 'En az 8 karakter', 'zh_CN' => '至少8个字符', 'nl_BE' => 'Minstens 8 tekens'],
            'حرف صغير' => ['en' => 'Lowercase letter', 'fr' => 'Lettre minuscule', 'es' => 'Letra minúscula', 'de' => 'Kleinbuchstabe', 'tr' => 'Küçük harf', 'zh_CN' => '小写字母', 'nl_BE' => 'Kleine letter'],
            'حرف كبير' => ['en' => 'Uppercase letter', 'fr' => 'Lettre majuscule', 'es' => 'Letra mayúscula', 'de' => 'Großbuchstabe', 'tr' => 'Büyük harf', 'zh_CN' => '大写字母', 'nl_BE' => 'Hoofdletter'],
            'رقم' => ['en' => 'Number', 'fr' => 'Chiffre', 'es' => 'Número', 'de' => 'Zahl', 'tr' => 'Rakam', 'zh_CN' => '数字', 'nl_BE' => 'Cijfer'],
            'أعد كتابة كلمة المرور' => ['en' => 'Re-enter your password', 'fr' => 'Saisissez à nouveau le mot de passe', 'es' => 'Vuelve a escribir la contraseña', 'de' => 'Passwort erneut eingeben', 'tr' => 'Şifrenizi tekrar yazın', 'zh_CN' => '再次输入密码', 'nl_BE' => 'Voer uw wachtwoord opnieuw in'],
            'أوافق على شروط الاستخدام وسياسة الخصوصية.' => ['en' => 'I agree to the terms of use and privacy policy.', 'fr' => "J'accepte les conditions d'utilisation et la politique de confidentialité.", 'es' => 'Acepto los términos de uso y la política de privacidad.', 'de' => 'Ich akzeptiere die Nutzungsbedingungen und Datenschutzrichtlinie.', 'tr' => 'Kullanım şartlarını ve gizlilik politikasını kabul ediyorum.', 'zh_CN' => '我同意使用条款和隐私政策。', 'nl_BE' => 'Ik ga akkoord met de gebruiksvoorwaarden en het privacybeleid.'],
            'كلمتا المرور متطابقتان ✓' => ['en' => 'Passwords match ✓', 'fr' => 'Les mots de passe correspondent ✓', 'es' => 'Las contraseñas coinciden ✓', 'de' => 'Passwörter stimmen überein ✓', 'tr' => 'Şifreler eşleşiyor ✓', 'zh_CN' => '密码匹配 ✓', 'nl_BE' => 'Wachtwoorden komen overeen ✓'],
            'كلمتا المرور غير متطابقتين' => ['en' => 'Passwords do not match', 'fr' => 'Les mots de passe ne correspondent pas', 'es' => 'Las contraseñas no coinciden', 'de' => 'Passwörter stimmen nicht überein', 'tr' => 'Şifreler eşleşmiyor', 'zh_CN' => '密码不匹配', 'nl_BE' => 'Wachtwoorden komen niet overeen'],
            'إظهار كلمة المرور' => ['en' => 'Show password', 'fr' => 'Afficher le mot de passe', 'es' => 'Mostrar contraseña', 'de' => 'Passwort anzeigen', 'tr' => 'Şifreyi göster', 'zh_CN' => '显示密码', 'nl_BE' => 'Wachtwoord tonen'],
            'إخفاء كلمة المرور' => ['en' => 'Hide password', 'fr' => 'Masquer le mot de passe', 'es' => 'Ocultar contraseña', 'de' => 'Passwort ausblenden', 'tr' => 'Şifreyi gizle', 'zh_CN' => '隐藏密码', 'nl_BE' => 'Wachtwoord verbergen'],
            'إظهار تأكيد كلمة المرور' => ['en' => 'Show password confirmation', 'fr' => 'Afficher la confirmation du mot de passe', 'es' => 'Mostrar confirmación de contraseña', 'de' => 'Passwortbestätigung anzeigen', 'tr' => 'Şifre onayını göster', 'zh_CN' => '显示密码确认', 'nl_BE' => 'Wachtwoordbevestiging tonen'],
            'إخفاء تأكيد كلمة المرور' => ['en' => 'Hide password confirmation', 'fr' => 'Masquer la confirmation du mot de passe', 'es' => 'Ocultar confirmación de contraseña', 'de' => 'Passwortbestätigung ausblenden', 'tr' => 'Şifre onayını gizle', 'zh_CN' => '隐藏密码确认', 'nl_BE' => 'Wachtwoordbevestiging verbergen'],
            'يجب الموافقة قبل إنشاء الحساب.' => ['en' => 'You must agree before creating an account.', 'fr' => 'Vous devez accepter avant de créer un compte.', 'es' => 'Debes aceptar antes de crear la cuenta.', 'de' => 'Sie müssen vor der Kontoerstellung zustimmen.', 'tr' => 'Hesap oluşturmadan önce kabul etmelisiniz.', 'zh_CN' => '创建账户前必须同意。', 'nl_BE' => 'U moet akkoord gaan voordat u een account aanmaakt.'],
            'الإدارة' => ['en' => 'Administration', 'fr' => 'Administration', 'es' => 'Administración', 'de' => 'Verwaltung', 'tr' => 'Yönetim', 'zh_CN' => '管理', 'nl_BE' => 'Beheer'],
            'المستخدمون والصلاحيات' => ['en' => 'Users & Permissions', 'fr' => 'Utilisateurs et permissions', 'es' => 'Usuarios y permisos', 'de' => 'Benutzer und Berechtigungen', 'tr' => 'Kullanıcılar ve izinler', 'zh_CN' => '用户和权限', 'nl_BE' => 'Gebruikers en machtigingen'],
            'الدفعات' => ['en' => 'Payments', 'fr' => 'Paiements', 'es' => 'Pagos', 'de' => 'Zahlungen', 'tr' => 'Ödemeler', 'zh_CN' => '付款', 'nl_BE' => 'Betalingen'],
            'طرق الدفع' => ['en' => 'Payment Methods', 'fr' => 'Modes de paiement', 'es' => 'Métodos de pago', 'de' => 'Zahlungsmethoden', 'tr' => 'Ödeme yöntemleri', 'zh_CN' => '支付方式', 'nl_BE' => 'Betaalmethoden'],
            'نظام إدارة الأعمال والمنشآت الذكي' => ['en' => 'Smart Business & Facility Management', 'fr' => 'Gestion intelligente des entreprises et installations', 'es' => 'Gestión inteligente de empresas e instalaciones', 'de' => 'Intelligente Geschäfts- und Anlagenverwaltung', 'tr' => 'Akıllı işletme ve tesis yönetimi', 'zh_CN' => '智能业务和设施管理', 'nl_BE' => 'Slim bedrijfs- en faciliteitenbeheer'],
            'مدير النظام' => ['en' => 'System Administrator', 'fr' => 'Administrateur système', 'es' => 'Administrador del sistema', 'de' => 'Systemadministrator', 'tr' => 'Sistem yöneticisi', 'zh_CN' => '系统管理员', 'nl_BE' => 'Systeembeheerder'],
            'تم تسجيل الدخول بنجاح.' => ['en' => 'Logged in successfully.', 'fr' => 'Connexion réussie.', 'es' => 'Inicio de sesión correcto.', 'de' => 'Erfolgreich angemeldet.', 'tr' => 'Başarıyla giriş yapıldı.', 'zh_CN' => '登录成功。', 'nl_BE' => 'Succesvol ingelogd.'],
            'مرحباً' => ['en' => 'Welcome', 'fr' => 'Bienvenue', 'es' => 'Bienvenido', 'de' => 'Willkommen', 'tr' => 'Hoş geldiniz', 'zh_CN' => '欢迎', 'nl_BE' => 'Welkom'],
            'شركة' => ['en' => 'Company', 'fr' => 'Entreprise', 'es' => 'Empresa', 'de' => 'Unternehmen', 'tr' => 'Şirket', 'zh_CN' => '公司', 'nl_BE' => 'Bedrijf'],
            'تكلفة المخزون' => ['en' => 'Inventory cost', 'fr' => 'Coût du stock', 'es' => 'Coste del inventario', 'de' => 'Inventarkosten', 'tr' => 'Stok maliyeti', 'zh_CN' => '库存成本', 'nl_BE' => 'Voorraadkosten'],
            'القيمة البيعية' => ['en' => 'Sales value', 'fr' => 'Valeur de vente', 'es' => 'Valor de venta', 'de' => 'Verkaufswert', 'tr' => 'Satış değeri', 'zh_CN' => '销售价值', 'nl_BE' => 'Verkoopwaarde'],
            'الربح المتوقع' => ['en' => 'Expected profit', 'fr' => 'Bénéfice attendu', 'es' => 'Beneficio esperado', 'de' => 'Erwarteter Gewinn', 'tr' => 'Beklenen kâr', 'zh_CN' => '预期利润', 'nl_BE' => 'Verwachte winst'],
            'المنتج' => ['en' => 'Product', 'fr' => 'Produit', 'es' => 'Producto', 'de' => 'Produkt', 'tr' => 'Ürün', 'zh_CN' => '产品', 'nl_BE' => 'Product'],
            'المستودع' => ['en' => 'Warehouse', 'fr' => 'Entrepôt', 'es' => 'Almacén', 'de' => 'Lager', 'tr' => 'Depo', 'zh_CN' => '仓库', 'nl_BE' => 'Magazijn'],
            'الكمية' => ['en' => 'Quantity', 'fr' => 'Quantité', 'es' => 'Cantidad', 'de' => 'Menge', 'tr' => 'Miktar', 'zh_CN' => '数量', 'nl_BE' => 'Hoeveelheid'],
            'وارد' => ['en' => 'Incoming', 'fr' => 'Entrant', 'es' => 'Entrant', 'de' => 'Eingang', 'tr' => 'Giriş', 'zh_CN' => '入库', 'nl_BE' => 'Inkomend'],
            'صادر' => ['en' => 'Outgoing', 'fr' => 'Sortant', 'es' => 'Salant', 'de' => 'Ausgang', 'tr' => 'Çıkış', 'zh_CN' => '出库', 'nl_BE' => 'Uitgaand'],
            'تسوية' => ['en' => 'Adjustment', 'fr' => 'Ajustement', 'es' => 'Ajuste', 'de' => 'Anpassung', 'tr' => 'Düzeltme', 'zh_CN' => '调整', 'nl_BE' => 'Aanpassing'],
            'منشأة' => ['en' => 'Facility', 'fr' => 'Installation', 'es' => 'Instalación', 'de' => 'Einrichtung', 'tr' => 'Tesis', 'zh_CN' => '设施', 'nl_BE' => 'Faciliteit'],
            'المنشآت' => ['en' => 'Facilities', 'fr' => 'Installations', 'es' => 'Instalaciones', 'de' => 'Einrichtungen', 'tr' => 'Tesisler', 'zh_CN' => '设施', 'nl_BE' => 'Faciliteiten'],
            'نشطة' => ['en' => 'Active', 'fr' => 'Active', 'es' => 'Activa', 'de' => 'Aktiv', 'tr' => 'Aktif', 'zh_CN' => '活跃', 'nl_BE' => 'Actief'],
            'متوقفة' => ['en' => 'Inactive', 'fr' => 'Inactive', 'es' => 'Inactiva', 'de' => 'Inaktiv', 'tr' => 'Pasif', 'zh_CN' => '停用', 'nl_BE' => 'Inactief'],
            'الموظفون والعمال وحراس الأمن' => ['en' => 'Employees, workers, and security guards', 'fr' => 'Employés, ouvriers et agents de sécurité', 'es' => 'Empleados, trabajadores y guardias de seguridad', 'de' => 'Mitarbeiter, Arbeiter und Sicherheitskräfte', 'tr' => 'Çalışanlar, işçiler ve güvenlik görevlileri', 'zh_CN' => '员工、工人和保安', 'nl_BE' => 'Werknemers, arbeiders en beveiligers'],
            'إدارة كل شخص داخل الشركة أو العقار.' => ['en' => 'Manage every person in the company or property.', 'fr' => 'Gérez chaque personne dans l’entreprise ou la propriété.', 'es' => 'Gestiona a cada persona de la empresa o propiedad.', 'de' => 'Verwalten Sie jede Person im Unternehmen oder Objekt.', 'tr' => 'Şirketteki veya tesisteki herkesi yönetin.', 'zh_CN' => '管理公司或物业中的每个人。', 'nl_BE' => 'Beheer iedereen binnen het bedrijf of eigendom.'],
            'اختر' => ['en' => 'Choose', 'fr' => 'Choisir', 'es' => 'Elegir', 'de' => 'Auswählen', 'tr' => 'Seç', 'zh_CN' => '选择', 'nl_BE' => 'Kiezen'],
            'كل الأنواع' => ['en' => 'All types', 'fr' => 'Tous les types', 'es' => 'Todos los tipos', 'de' => 'Alle Typen', 'tr' => 'Tüm türler', 'zh_CN' => '所有类型', 'nl_BE' => 'Alle typen'],
            'كل الحالات' => ['en' => 'All statuses', 'fr' => 'Tous les statuts', 'es' => 'Todos los estados', 'de' => 'Alle Status', 'tr' => 'Tüm durumlar', 'zh_CN' => '所有状态', 'nl_BE' => 'Alle statussen'],
            'موظف' => ['en' => 'Employee', 'fr' => 'Employé', 'es' => 'Empleado', 'de' => 'Mitarbeiter', 'tr' => 'Çalışan', 'zh_CN' => '员工', 'nl_BE' => 'Werknemer'],
            'عامل' => ['en' => 'Worker', 'fr' => 'Ouvrier', 'es' => 'Trabajador', 'de' => 'Arbeiter', 'tr' => 'İşçi', 'zh_CN' => '工人', 'nl_BE' => 'Arbeider'],
            'حارس أمن' => ['en' => 'Security guard', 'fr' => 'Agent de sécurité', 'es' => 'Guardia de seguridad', 'de' => 'Sicherheitskraft', 'tr' => 'Güvenlik görevlisi', 'zh_CN' => '保安', 'nl_BE' => 'Beveiliger'],
            'مدير' => ['en' => 'Manager', 'fr' => 'Responsable', 'es' => 'Gerente', 'de' => 'Manager', 'tr' => 'Yönetici', 'zh_CN' => '经理', 'nl_BE' => 'Manager'],
            'مشرف' => ['en' => 'Supervisor', 'fr' => 'Superviseur', 'es' => 'Supervisor', 'de' => 'Supervisor', 'tr' => 'Süpervizör', 'zh_CN' => '主管', 'nl_BE' => 'Toezichthouder'],
            'محاسب' => ['en' => 'Accountant', 'fr' => 'Comptable', 'es' => 'Contable', 'de' => 'Buchhalter', 'tr' => 'Muhasebeci', 'zh_CN' => '会计', 'nl_BE' => 'Accountant'],
            'سائق' => ['en' => 'Driver', 'fr' => 'Chauffeur', 'es' => 'Conductor', 'de' => 'Fahrer', 'tr' => 'Şoför', 'zh_CN' => '司机', 'nl_BE' => 'Chauffeur'],
            'فني' => ['en' => 'Technician', 'fr' => 'Technicien', 'es' => 'Técnico', 'de' => 'Techniker', 'tr' => 'Teknisyen', 'zh_CN' => '技术员', 'nl_BE' => 'Technicus'],
            'مبيعات' => ['en' => 'Sales', 'fr' => 'Ventes', 'es' => 'Ventas', 'de' => 'Vertrieb', 'tr' => 'Satış', 'zh_CN' => '销售', 'nl_BE' => 'Verkoop'],
            'بشرية' => ['en' => 'Human Resources', 'fr' => 'Ressources humaines', 'es' => 'Recursos humanos', 'de' => 'Personalwesen', 'tr' => 'İnsan kaynakları', 'zh_CN' => '人力资源', 'nl_BE' => 'Human resources'],
            'مقاول' => ['en' => 'Contractor', 'fr' => 'Prestataire', 'es' => 'Contratista', 'de' => 'Auftragnehmer', 'tr' => 'Yüklenici', 'zh_CN' => '承包商', 'nl_BE' => 'Aannemer'],
            'مستأجر' => ['en' => 'Tenant', 'fr' => 'Locataire', 'es' => 'Inquilino', 'de' => 'Mieter', 'tr' => 'Kiracı', 'zh_CN' => '租户', 'nl_BE' => 'Huurder'],
            'نشط' => ['en' => 'Active', 'fr' => 'Actif', 'es' => 'Activo', 'de' => 'Aktiv', 'tr' => 'Aktif', 'zh_CN' => '活跃', 'nl_BE' => 'Actief'],
            'موقوف' => ['en' => 'Suspended', 'fr' => 'Suspendu', 'es' => 'Suspendido', 'de' => 'Ausgesetzt', 'tr' => 'Askıya alınmış', 'zh_CN' => '暂停', 'nl_BE' => 'Opgeschort'],
            'منتهي الخدمة' => ['en' => 'Terminated', 'fr' => 'Fin de service', 'es' => 'Finalizado', 'de' => 'Beendet', 'tr' => 'İşten ayrılmış', 'zh_CN' => '已终止', 'nl_BE' => 'Beëindigd'],
            'الدوام الصباحي' => ['en' => 'Morning shift', 'fr' => 'Équipe du matin', 'es' => 'Turno de mañana', 'de' => 'Frühschicht', 'tr' => 'Sabah vardiyası', 'zh_CN' => '早班', 'nl_BE' => 'Ochtendploeg'],
            'الليلية' => ['en' => 'Night shift', 'fr' => 'Équipe de nuit', 'es' => 'Turno nocturno', 'de' => 'Nachtschicht', 'tr' => 'Gece vardiyası', 'zh_CN' => '夜班', 'nl_BE' => 'Nachtploeg'],
            'دقيقة' => ['en' => 'minutes', 'fr' => 'minutes', 'es' => 'minutos', 'de' => 'Minuten', 'tr' => 'dakika', 'zh_CN' => '分钟', 'nl_BE' => 'minuten'],
            'عرض' => ['en' => 'View', 'fr' => 'Voir', 'es' => 'Ver', 'de' => 'Anzeigen', 'tr' => 'Görüntüle', 'zh_CN' => '查看', 'nl_BE' => 'Bekijken'],
            'لا توجد سجلات لهذا اليوم.' => ['en' => 'No records for this day.', 'fr' => 'Aucun enregistrement pour cette journée.', 'es' => 'No hay registros para este día.', 'de' => 'Keine Aufzeichnungen für diesen Tag.', 'tr' => 'Bu gün için kayıt yok.', 'zh_CN' => '当天没有记录。', 'nl_BE' => 'Geen records voor deze dag.'],
            'التفاصيل' => ['en' => 'Details', 'fr' => 'Détails', 'es' => 'Detalles', 'de' => 'Details', 'tr' => 'Detaylar', 'zh_CN' => '详情', 'nl_BE' => 'Details'],
            'شخص' => ['en' => 'Person', 'fr' => 'Personne', 'es' => 'Persona', 'de' => 'Person', 'tr' => 'Kişi', 'zh_CN' => '人员', 'nl_BE' => 'Persoon'],
            'الدوام الرسمي' => ['en' => 'Scheduled hours', 'fr' => 'Horaires prévus', 'es' => 'Horario programado', 'de' => 'Planmäßige Arbeitszeit', 'tr' => 'Planlanan çalışma', 'zh_CN' => '计划工时', 'nl_BE' => 'Geplande uren'],
            'الدخول' => ['en' => 'Check-in', 'fr' => 'Entrée', 'es' => 'Entrada', 'de' => 'Eintritt', 'tr' => 'Giriş', 'zh_CN' => '签到', 'nl_BE' => 'Inchecken'],
            'الخروج' => ['en' => 'Check-out', 'fr' => 'Sortie', 'es' => 'Salida', 'de' => 'Austritt', 'tr' => 'Çıkış', 'zh_CN' => '签退', 'nl_BE' => 'Uitchecken'],
            'العمل' => ['en' => 'Work', 'fr' => 'Travail', 'es' => 'Trabajo', 'de' => 'Arbeit', 'tr' => 'Çalışma', 'zh_CN' => '工作', 'nl_BE' => 'Werk'],
            'التأخير' => ['en' => 'Late arrival', 'fr' => 'Retard', 'es' => 'Retraso', 'de' => 'Verspätung', 'tr' => 'Gecikme', 'zh_CN' => '迟到', 'nl_BE' => 'Vertraging'],
            'تسجيل زائر' => ['en' => 'Register visitor', 'fr' => 'Enregistrer un visiteur', 'es' => 'Registrar visitante', 'de' => 'Besucher registrieren', 'tr' => 'Ziyaretçi kaydet', 'zh_CN' => '登记访客', 'nl_BE' => 'Bezoeker registreren'],
            'الزائر' => ['en' => 'Visitor', 'fr' => 'Visiteur', 'es' => 'Visitante', 'de' => 'Besucher', 'tr' => 'Ziyaretçi', 'zh_CN' => '访客', 'nl_BE' => 'Bezoeker'],
            'الجهة المستضيفة' => ['en' => 'Host department', 'fr' => 'Service d’accueil', 'es' => 'Departamento anfitrión', 'de' => 'Gastgebende Abteilung', 'tr' => 'Ev sahibi departman', 'zh_CN' => '接待部门', 'nl_BE' => 'Ontvangende afdeling'],
            'الهاتف' => ['en' => 'Phone', 'fr' => 'Téléphone', 'es' => 'Teléfono', 'de' => 'Telefon', 'tr' => 'Telefon', 'zh_CN' => '电话', 'nl_BE' => 'Telefoon'],
            'الحركة' => ['en' => 'Movement', 'fr' => 'Mouvement', 'es' => 'Movimiento', 'de' => 'Beweging', 'tr' => 'Hareket', 'zh_CN' => '动向', 'nl_BE' => 'Beweging'],
            'البوابة' => ['en' => 'Gate', 'fr' => 'Portail', 'es' => 'Puerta', 'de' => 'Tor', 'tr' => 'Kapı', 'zh_CN' => '门', 'nl_BE' => 'Poort'],
            'الوقت' => ['en' => 'Time', 'fr' => 'Heure', 'es' => 'Hora', 'de' => 'Zeit', 'tr' => 'Saat', 'zh_CN' => '时间', 'nl_BE' => 'Tijd'],
            'المسجل' => ['en' => 'Recorded by', 'fr' => 'Enregistré par', 'es' => 'Registrado por', 'de' => 'Erfasst von', 'tr' => 'Kaydeden', 'zh_CN' => '记录人', 'nl_BE' => 'Geregistreerd door'],
            'اجتماع' => ['en' => 'Meeting', 'fr' => 'Réunion', 'es' => 'Reunión', 'de' => 'Besprechung', 'tr' => 'Toplantı', 'zh_CN' => '会议', 'nl_BE' => 'Vergadering'],
            'إدارة' => ['en' => 'Manage', 'fr' => 'Gérer', 'es' => 'Gestionar', 'de' => 'Verwalten', 'tr' => 'Yönet', 'zh_CN' => '管理', 'nl_BE' => 'Beheren'],
            'المستخدم' => ['en' => 'User', 'fr' => 'Utilisateur', 'es' => 'Usuario', 'de' => 'Benutzer', 'tr' => 'Kullanıcı', 'zh_CN' => '用户', 'nl_BE' => 'Gebruiker'],
            'الدور' => ['en' => 'Role', 'fr' => 'Rôle', 'es' => 'Rol', 'de' => 'Rolle', 'tr' => 'Rol', 'zh_CN' => '角色', 'nl_BE' => 'Rol'],
            'آخر دخول' => ['en' => 'Last login', 'fr' => 'Dernière connexion', 'es' => 'Último acceso', 'de' => 'Letzte Anmeldung', 'tr' => 'Son giriş', 'zh_CN' => '上次登录', 'nl_BE' => 'Laatste login'],
            'سجل' => ['en' => 'Log', 'fr' => 'Journal', 'es' => 'Registro', 'de' => 'Protokoll', 'tr' => 'Kayıt', 'zh_CN' => '日志', 'nl_BE' => 'Logboek'],
            'تحديد دور المستخدم و' => ['en' => 'Specify the user role and ', 'fr' => 'Définissez le rôle de l’utilisateur et ', 'es' => 'Define el rol del usuario y ', 'de' => 'Legen Sie die Benutzerrolle und ', 'tr' => 'Kullanıcı rolünü ve ', 'zh_CN' => '指定用户角色和', 'nl_BE' => 'Bepaal de gebruikersrol en '],
            'التي يعمل بها.' => ['en' => ' where they work.', 'fr' => ' où il travaille.', 'es' => ' donde trabaja.', 'de' => ' den Arbeitsplatz fest.', 'tr' => ' çalışma yerini belirleyin.', 'zh_CN' => '其工作地点。', 'nl_BE' => ' waar deze werkt.'],
            'للتجارة' => ['en' => 'Trading', 'fr' => 'Commerce', 'es' => 'Comercio', 'de' => 'Handel', 'tr' => 'Ticaret', 'zh_CN' => '贸易', 'nl_BE' => 'Handel'],
        ];
    }

    private static function loadArabicKeys(): array
    {
        $translations = json_decode(
            file_get_contents(resource_path('lang/ar.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $keys = [];
        foreach ($translations as $key => $value) {
            if (is_string($value)) {
                $keys[$value] = $key;
            }
        }

        return $keys;
    }
}
