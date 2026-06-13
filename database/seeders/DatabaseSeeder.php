<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AdresseLivraison;
use App\Models\EntrepriseInformation;
use App\Models\EntrepriseDomain;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Models\Signataire;
use App\Models\Commande;
use App\Models\CommandeProduct;
use App\Models\Support;
use App\Models\SupportMessage;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création de l'entreprise
        $entreprise = EntrepriseInformation::create([
            'raison_sociale' => 'Manystacks Demo',
            'siret' => '12345678901234',
            'adresse' => '123 Avenue des Champs-Élysées',
            'complement_adresse' => 'Bâtiment A',
            'code_postal' => '75008',
            'ville' => 'Paris',
            'pays' => 'France',
            'auto_entreprise' => false,
            'licence_google' => true,
            'licence_microsoft' => true,
        ]);

        // Création d'un domaine pour l'entreprise
        EntrepriseDomain::create([
            'entreprise_id' => $entreprise->id,
            'domain' => 'manystacks.co',
            'tenant' => 'manystacks',
        ]);

        // Création d'une adresse de livraison principale
        $adresse = AdresseLivraison::create([
            'entreprise_id' => $entreprise->id,
            'primary' => true,
            'default' => true,
            'titre' => 'Siège Social',
            'adresse' => '123 Avenue des Champs-Élysées',
            'complement_adresse' => 'Bâtiment A',
            'code_postal' => '75008',
            'ville' => 'Paris',
            'pays' => 'France',
        ]);

        // Création d'une adresse de livraison secondaire
        AdresseLivraison::create([
            'entreprise_id' => $entreprise->id,
            'primary' => false,
            'default' => false,
            'titre' => 'Bureau Lyon',
            'adresse' => '45 Rue de la République',
            'code_postal' => '69002',
            'ville' => 'Lyon',
            'pays' => 'France',
        ]);

        // Création d'un utilisateur admin
        $admin = User::create([
            'name' => 'Patrick',
            'email' => 'admin@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'poste' => 'Administrateur Système',
            'tel' => '+33 6 12 34 56 78',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'bienvenue' => true,
            'email_verified_at' => now(),
        ]);

        // Création d'utilisateurs supplémentaires
        $users = [];
        $users[] = $jean = User::create([
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'user',
            'poste' => 'Développeur Full-Stack',
            'tel' => '+33 6 23 45 67 89',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'date_arrivee' => '2024-01-15',
            'email_verified_at' => now(),
        ]);

        $users[] = $marie = User::create([
            'name' => 'Marie Martin',
            'email' => 'marie.martin@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'poste' => 'Chef de Projet',
            'tel' => '+33 6 34 56 78 90',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'date_arrivee' => '2023-09-01',
            'email_verified_at' => now(),
        ]);

        $users[] = $sophie = User::create([
            'name' => 'Sophie Bernard',
            'email' => 'sophie.bernard@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'user',
            'poste' => 'Designer UX/UI',
            'tel' => '+33 6 45 67 89 01',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'date_arrivee' => '2024-03-10',
            'email_verified_at' => now(),
        ]);

        $users[] = $thomas = User::create([
            'name' => 'Thomas Lefebvre',
            'email' => 'thomas.lefebvre@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'user',
            'poste' => 'DevOps Engineer',
            'tel' => '+33 6 56 78 90 12',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'date_arrivee' => '2023-11-20',
            'email_verified_at' => now(),
        ]);

        $users[] = $camille = User::create([
            'name' => 'Camille Dubois',
            'email' => 'camille.dubois@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'user',
            'poste' => 'Data Analyst',
            'tel' => '+33 6 67 89 01 23',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'date_arrivee' => '2024-02-05',
            'email_verified_at' => now(),
        ]);

        $users[] = $julien = User::create([
            'name' => 'Julien Moreau',
            'email' => 'julien.moreau@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'user',
            'poste' => 'Product Manager',
            'tel' => '+33 6 78 90 12 34',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'date_arrivee' => '2023-08-15',
            'email_verified_at' => now(),
        ]);

        $users[] = $laura = User::create([
            'name' => 'Laura Petit',
            'email' => 'laura.petit@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'user',
            'poste' => 'Marketing Manager',
            'tel' => '+33 6 89 01 23 45',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'date_arrivee' => '2024-01-08',
            'email_verified_at' => now(),
        ]);

        $users[] = $nicolas = User::create([
            'name' => 'Nicolas Roux',
            'email' => 'nicolas.roux@manystacks.co',
            'password' => Hash::make('password'),
            'role' => 'user',
            'poste' => 'Développeur Backend',
            'tel' => '+33 6 90 12 34 56',
            'entreprise_id' => $entreprise->id,
            'adresse_id' => $adresse->id,
            'type' => 'Personne',
            'date_arrivee' => '2023-12-01',
            'email_verified_at' => now(),
        ]);

        // Création d'un signataire
        $signataire = Signataire::create([
            'entreprise_id' => $entreprise->id,
            'nom' => 'Demo',
            'prenom' => 'Admin',
            'mail' => 'admin@manystacks.co',
            'representant_legal' => true,
        ]);

        // Création de tags
        $tagOrdinateur = Tag::create(['name' => 'Ordinateur']);
        $tagTelephonie = Tag::create(['name' => 'Téléphonie']);
        $tagAccessoire = Tag::create(['name' => 'Accessoire']);
        $tagLicence = Tag::create(['name' => 'Licence']);

        // Création de produits - Ordinateurs portables
        $macbookPro = Product::create([
            'name' => 'MacBook Pro 14" M3 Pro',
            'slug' => 'macbook-pro-14-m3-pro',
            'categorie' => 'Ordinateur',
            'sous_categorie' => 'Ordinateur portable',
            'marque' => 'Apple',
            'modele' => 'MacBook Pro 14" 2024',
            'description' => 'Le MacBook Pro 14" avec puce M3 Pro offre des performances exceptionnelles pour les professionnels créatifs et techniques.',
            'processeur' => 'Apple M3 Pro 11 cœurs',
            'ram' => '18 Go',
            'stockage' => '512 Go',
            'type_stockage' => 'SSD',
            'taille_ecran' => '14.2"',
            'type_ecran' => 'Liquid Retina XDR',
            'resolution_ecran' => '3024 x 1964',
            'carte_graphique' => 'GPU 14 cœurs',
            'systeme_exploitation' => 'macOS Sonoma',
            'connectivite' => 'Wi-Fi 6E, Bluetooth 5.3',
            'connectique' => '3x Thunderbolt 4, HDMI 2.1, Jack 3.5mm, MagSafe 3',
            'autonomie_batterie' => 'Jusqu\'à 18 heures',
            'poids' => '1.55 kg',
            'couleur' => 'Gris sidéral',
            'garantie' => '1 an',
            'etat' => 'Neuf',
            'prix_achat' => 2499.00,
            'fournisseur' => 'Apple',
            'delais_livraison' => '3-5 jours',
            'top_produit' => true,
            'co2' => true,
            'empreinte_carbonne' => '85 kg CO2e',
        ]);

        ProductImage::create([
            'product_id' => $macbookPro->id,
            'image_url' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/mbp14-spacegray-select-202310?wid=904&hei=840&fmt=jpeg&qlt=90',
            'principale' => true,
        ]);

        $dellLatitude = Product::create([
            'name' => 'Dell Latitude 7440',
            'slug' => 'dell-latitude-7440',
            'categorie' => 'Ordinateur',
            'sous_categorie' => 'Ordinateur portable',
            'marque' => 'Dell',
            'modele' => 'Latitude 7440',
            'description' => 'Ordinateur portable professionnel ultra-portable avec sécurité renforcée.',
            'processeur' => 'Intel Core i7-1365U',
            'ram' => '16 Go',
            'stockage' => '512 Go',
            'type_stockage' => 'SSD NVMe',
            'taille_ecran' => '14"',
            'type_ecran' => 'FHD Anti-reflets',
            'resolution_ecran' => '1920 x 1080',
            'carte_graphique' => 'Intel Iris Xe',
            'systeme_exploitation' => 'Windows 11 Pro',
            'connectivite' => 'Wi-Fi 6E, Bluetooth 5.3, 4G LTE',
            'connectique' => '2x Thunderbolt 4, 2x USB-A 3.2, HDMI 2.0, RJ45, Jack',
            'autonomie_batterie' => 'Jusqu\'à 12 heures',
            'poids' => '1.36 kg',
            'couleur' => 'Noir',
            'garantie' => '3 ans ProSupport',
            'etat' => 'Neuf',
            'prix_achat' => 1899.00,
            'fournisseur' => 'Dell',
            'delais_livraison' => '5-7 jours',
            'top_produit' => true,
            'co2' => true,
            'empreinte_carbonne' => '72 kg CO2e',
        ]);

        // Création de produits - Smartphones
        $iphone15Pro = Product::create([
            'name' => 'iPhone 15 Pro 256 Go',
            'slug' => 'iphone-15-pro-256go',
            'categorie' => 'Téléphonie',
            'sous_categorie' => 'Smartphone',
            'marque' => 'Apple',
            'modele' => 'iPhone 15 Pro',
            'description' => 'L\'iPhone 15 Pro avec puce A17 Pro et cadre en titane.',
            'processeur' => 'Apple A17 Pro',
            'ram' => '8 Go',
            'stockage' => '256 Go',
            'taille_ecran' => '6.1"',
            'type_ecran' => 'Super Retina XDR OLED',
            'resolution_ecran' => '2556 x 1179',
            'systeme_exploitation' => 'iOS 17',
            'camera' => 'Triple caméra',
            'carac_camera' => 'Principal 48MP, Ultra grand-angle 12MP, Téléobjectif 12MP',
            'connectivite' => '5G, Wi-Fi 6E, Bluetooth 5.3, NFC',
            'autonomie_batterie' => 'Jusqu\'à 23 heures',
            'poids' => '187 g',
            'couleur' => 'Titane naturel',
            'garantie' => '1 an',
            'etat' => 'Neuf',
            'prix_achat' => 1229.00,
            'fournisseur' => 'Apple',
            'delais_livraison' => '2-3 jours',
            'top_produit' => true,
        ]);

        ProductImage::create([
            'product_id' => $iphone15Pro->id,
            'image_url' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-1inch-naturaltitanium?wid=5120&hei=2880&fmt=p-jpg&qlt=80',
            'principale' => true,
        ]);

        $samsungS24 = Product::create([
            'name' => 'Samsung Galaxy S24 Ultra 512 Go',
            'slug' => 'samsung-galaxy-s24-ultra-512go',
            'categorie' => 'Téléphonie',
            'sous_categorie' => 'Smartphone',
            'marque' => 'Samsung',
            'modele' => 'Galaxy S24 Ultra',
            'description' => 'Le smartphone le plus puissant de Samsung avec S Pen intégré.',
            'processeur' => 'Snapdragon 8 Gen 3',
            'ram' => '12 Go',
            'stockage' => '512 Go',
            'taille_ecran' => '6.8"',
            'type_ecran' => 'Dynamic AMOLED 2X',
            'resolution_ecran' => '3120 x 1440',
            'systeme_exploitation' => 'Android 14',
            'camera' => 'Quad caméra',
            'carac_camera' => 'Principal 200MP, Ultra grand-angle 12MP, Téléobjectif 50MP + 10MP',
            'connectivite' => '5G, Wi-Fi 7, Bluetooth 5.3, NFC',
            'autonomie_batterie' => 'Jusqu\'à 25 heures',
            'poids' => '232 g',
            'couleur' => 'Titanium Black',
            'garantie' => '2 ans',
            'etat' => 'Neuf',
            'prix_achat' => 1459.00,
            'fournisseur' => 'Samsung',
            'delais_livraison' => '3-5 jours',
            'top_produit' => true,
        ]);

        // Création de produits - Écrans
        $dellMonitor = Product::create([
            'name' => 'Dell UltraSharp U2723DE',
            'slug' => 'dell-ultrasharp-u2723de',
            'categorie' => 'Accessoire',
            'sous_categorie' => 'Écran',
            'marque' => 'Dell',
            'modele' => 'UltraSharp U2723DE',
            'description' => 'Écran 27" QHD avec hub USB-C intégré pour une productivité maximale.',
            'taille_ecran' => '27"',
            'type_ecran' => 'IPS',
            'resolution_ecran' => '2560 x 1440',
            'frequence_ecran' => '60 Hz',
            'temps_reponse_ecran' => '5 ms',
            'luminosite_ecran' => '350 cd/m²',
            'connectique' => 'USB-C (90W), HDMI 2.0, DisplayPort 1.4, 4x USB-A, RJ45',
            'dimension' => '611 x 366 x 185 mm',
            'poids' => '5.5 kg',
            'garantie' => '3 ans Premium Panel Exchange',
            'etat' => 'Neuf',
            'prix_achat' => 549.00,
            'fournisseur' => 'Dell',
            'delais_livraison' => '5-7 jours',
            'co2' => true,
        ]);

        ProductImage::create([
            'product_id' => $dellMonitor->id,
            'image_url' => '/images/created_ecrans_icon.svg',
            'principale' => true,
        ]);

        // Création de produits - Licences logicielles
        $office365 = Product::create([
            'name' => 'Microsoft 365 Business Standard',
            'slug' => 'microsoft-365-business-standard',
            'categorie' => 'licences',
            'sous_categorie' => 'licences',
            'marque' => 'Microsoft',
            'description' => 'Suite complète d\'outils de productivité et de collaboration pour entreprise.',
            'type_licence' => 'Mensuel',
            'prix_achat' => 10.50,
            'prix_location' => 10.50,
            'fournisseur' => 'Microsoft',
            'appsincluses' => 'Word, Excel, PowerPoint, Outlook, Teams, OneDrive, SharePoint, Exchange',
            'appstype' => 'Cloud',
            'delais_livraison' => 'Immédiat',
            'etat' => 'Neuf',
        ]);

        ProductImage::create([
            'product_id' => $office365->id,
            'image_url' => '/images/microsoft-logo.png',
            'principale' => true,
        ]);

        $googleWorkspace = Product::create([
            'name' => 'Google Workspace Business Standard',
            'slug' => 'google-workspace-business-standard',
            'categorie' => 'licences',
            'sous_categorie' => 'licences',
            'marque' => 'Google',
            'description' => 'Suite Google Workspace pour la collaboration et la productivité en entreprise.',
            'type_licence' => 'Mensuel',
            'prix_achat' => 10.20,
            'prix_location' => 10.20,
            'fournisseur' => 'Google',
            'appsincluses' => 'Gmail, Drive, Docs, Sheets, Slides, Meet, Calendar, Chat',
            'appstype' => 'Cloud',
            'delais_livraison' => 'Immédiat',
            'etat' => 'Neuf',
        ]);

        ProductImage::create([
            'product_id' => $googleWorkspace->id,
            'image_url' => '/images/google-logo.png',
            'principale' => true,
        ]);

        // Création de produits - Accessoires
        $logitechKeyboard = Product::create([
            'name' => 'Logitech MX Keys',
            'slug' => 'logitech-mx-keys',
            'categorie' => 'Accessoire',
            'sous_categorie' => 'Clavier',
            'marque' => 'Logitech',
            'modele' => 'MX Keys',
            'description' => 'Clavier sans fil rétroéclairé pour une frappe confortable et précise.',
            'connectivite' => 'Bluetooth, USB',
            'autonomie_batterie' => 'Jusqu\'à 10 jours avec rétroéclairage',
            'couleur' => 'Gris graphite',
            'dimension' => '430.2 x 131.63 x 20.5 mm',
            'poids' => '810 g',
            'garantie' => '2 ans',
            'etat' => 'Neuf',
            'prix_achat' => 119.00,
            'fournisseur' => 'Logitech',
            'delais_livraison' => '2-3 jours',
        ]);

        ProductImage::create([
            'product_id' => $logitechKeyboard->id,
            'image_url' => '/images/created_claviers_icon.svg',
            'principale' => true,
        ]);

        $logitechMouse = Product::create([
            'name' => 'Logitech MX Master 3S',
            'slug' => 'logitech-mx-master-3s',
            'categorie' => 'Accessoire',
            'sous_categorie' => 'Souris',
            'marque' => 'Logitech',
            'modele' => 'MX Master 3S',
            'description' => 'Souris sans fil ergonomique avec capteur 8000 DPI ultra-précis et silencieux.',
            'connectivite' => 'Bluetooth, USB',
            'autonomie_batterie' => 'Jusqu\'à 70 jours',
            'couleur' => 'Gris graphite',
            'dimension' => '124.9 x 84.3 x 51 mm',
            'poids' => '141 g',
            'garantie' => '2 ans',
            'etat' => 'Neuf',
            'prix_achat' => 109.00,
            'fournisseur' => 'Logitech',
            'delais_livraison' => '2-3 jours',
        ]);

        ProductImage::create([
            'product_id' => $logitechMouse->id,
            'image_url' => '/images/created_souris_icon.svg',
            'principale' => true,
        ]);

        $jblHeadset = Product::create([
            'name' => 'Jabra Evolve2 65',
            'slug' => 'jabra-evolve2-65',
            'categorie' => 'Accessoire',
            'sous_categorie' => 'Casque audio',
            'marque' => 'Jabra',
            'modele' => 'Evolve2 65',
            'description' => 'Casque sans fil professionnel avec réduction de bruit active et certification Microsoft Teams.',
            'connectivite' => 'Bluetooth 5.0, USB-A',
            'audio' => 'Stéréo, Réduction de bruit active',
            'autonomie_batterie' => 'Jusqu\'à 37 heures',
            'poids' => '176 g',
            'couleur' => 'Noir',
            'garantie' => '2 ans',
            'etat' => 'Neuf',
            'prix_achat' => 239.00,
            'fournisseur' => 'Jabra',
            'delais_livraison' => '3-5 jours',
        ]);

        ProductImage::create([
            'product_id' => $jblHeadset->id,
            'image_url' => '/images/created_casques-micros_icon.svg',
            'principale' => true,
        ]);

        // Création de commandes et attribution d'équipements/licences

        // Commande 1 : Équipements pour l'équipe dev
        $commande1 = Commande::create([
            'reference_commande' => 'CMD-2024-001',
            'entreprise_id' => $entreprise->id,
            'signataire_id' => $signataire->id,
            'user_id' => $admin->id,
            'statut' => 'validée',
            'financeur' => 'Location',
            'date_debut_contrat' => Carbon::now()->subMonths(6),
            'date_fin_contrat' => Carbon::now()->addMonths(30),
            'date_validation' => Carbon::now()->subMonths(6),
        ]);

        // Attribuer MacBook Pro à Jean
        CommandeProduct::create([
            'commande_id' => $commande1->id,
            'entreprise_id' => $entreprise->id,
            'user_attributed_id' => $jean->id,
            'adresse_livraison_id' => $adresse->id,
            'name' => $macbookPro->name,
            'slug' => $macbookPro->slug,
            'categorie' => $macbookPro->categorie,
            'sous_categorie' => $macbookPro->sous_categorie,
            'marque' => $macbookPro->marque,
            'modele' => $macbookPro->modele,
            'processeur' => $macbookPro->processeur,
            'ram' => $macbookPro->ram,
            'stockage' => $macbookPro->stockage,
            'taille_ecran' => $macbookPro->taille_ecran,
            'systeme_exploitation' => $macbookPro->systeme_exploitation,
            'image_principale' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/mbp14-spacegray-select-202310?wid=904&hei=840&fmt=jpeg&qlt=90',
            'prix' => 69.42,
            'quantity' => 1,
            'type_contrat' => 'location',
            'status' => 'livré',
            'commande_status' => 'active',
            'fournisseur' => 'Apple',
            'etat' => 'Neuf',
        ]);

        // Attribuer Dell Latitude à Thomas
        CommandeProduct::create([
            'commande_id' => $commande1->id,
            'entreprise_id' => $entreprise->id,
            'user_attributed_id' => $thomas->id,
            'adresse_livraison_id' => $adresse->id,
            'name' => $dellLatitude->name,
            'slug' => $dellLatitude->slug,
            'categorie' => $dellLatitude->categorie,
            'sous_categorie' => $dellLatitude->sous_categorie,
            'marque' => $dellLatitude->marque,
            'modele' => $dellLatitude->modele,
            'processeur' => $dellLatitude->processeur,
            'ram' => $dellLatitude->ram,
            'stockage' => $dellLatitude->stockage,
            'taille_ecran' => $dellLatitude->taille_ecran,
            'systeme_exploitation' => $dellLatitude->systeme_exploitation,
            'image_principale' => '/images/created_ordinateurs_icon.svg',
            'prix' => 52.75,
            'quantity' => 1,
            'type_contrat' => 'location',
            'status' => 'livré',
            'commande_status' => 'active',
            'fournisseur' => 'Dell',
            'etat' => 'Neuf',
        ]);

        // Attribuer Dell Latitude à Nicolas
        CommandeProduct::create([
            'commande_id' => $commande1->id,
            'entreprise_id' => $entreprise->id,
            'user_attributed_id' => $nicolas->id,
            'adresse_livraison_id' => $adresse->id,
            'name' => $dellLatitude->name,
            'slug' => $dellLatitude->slug,
            'categorie' => $dellLatitude->categorie,
            'sous_categorie' => $dellLatitude->sous_categorie,
            'marque' => $dellLatitude->marque,
            'modele' => $dellLatitude->modele,
            'processeur' => $dellLatitude->processeur,
            'ram' => $dellLatitude->ram,
            'stockage' => $dellLatitude->stockage,
            'taille_ecran' => $dellLatitude->taille_ecran,
            'systeme_exploitation' => $dellLatitude->systeme_exploitation,
            'image_principale' => '/images/created_ordinateurs_icon.svg',
            'prix' => 52.75,
            'quantity' => 1,
            'type_contrat' => 'location',
            'status' => 'livré',
            'commande_status' => 'active',
            'fournisseur' => 'Dell',
            'etat' => 'Neuf',
        ]);

        // Commande 2 : Smartphones
        $commande2 = Commande::create([
            'reference_commande' => 'CMD-2024-002',
            'entreprise_id' => $entreprise->id,
            'signataire_id' => $signataire->id,
            'user_id' => $admin->id,
            'statut' => 'validée',
            'financeur' => 'achat',
            'date_debut_contrat' => Carbon::now(),
            'date_fin_contrat' => Carbon::now()->addMonths(36),
            'date_validation' => Carbon::now(),
        ]);

        // Attribuer iPhone 15 Pro à Marie
        CommandeProduct::create([
            'commande_id' => $commande2->id,
            'entreprise_id' => $entreprise->id,
            'user_attributed_id' => $marie->id,
            'adresse_livraison_id' => $adresse->id,
            'name' => $iphone15Pro->name,
            'slug' => $iphone15Pro->slug,
            'categorie' => $iphone15Pro->categorie,
            'sous_categorie' => $iphone15Pro->sous_categorie,
            'marque' => $iphone15Pro->marque,
            'modele' => $iphone15Pro->modele,
            'stockage' => $iphone15Pro->stockage,
            'systeme_exploitation' => $iphone15Pro->systeme_exploitation,
            'image_principale' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-1inch-naturaltitanium?wid=5120&hei=2880&fmt=p-jpg&qlt=80',
            'prix' => 1229.00,
            'quantity' => 1,
            'type_contrat' => 'achat',
            'status' => 'livré',
            'commande_status' => 'active',
            'fournisseur' => 'Apple',
            'etat' => 'Neuf',
        ]);

        // Attribuer Samsung S24 Ultra à Julien
        CommandeProduct::create([
            'commande_id' => $commande2->id,
            'entreprise_id' => $entreprise->id,
            'user_attributed_id' => $julien->id,
            'adresse_livraison_id' => $adresse->id,
            'name' => $samsungS24->name,
            'slug' => $samsungS24->slug,
            'categorie' => $samsungS24->categorie,
            'sous_categorie' => $samsungS24->sous_categorie,
            'marque' => $samsungS24->marque,
            'modele' => $samsungS24->modele,
            'stockage' => $samsungS24->stockage,
            'systeme_exploitation' => $samsungS24->systeme_exploitation,
            'image_principale' => '/images/created_telephones_icon.svg',
            'prix' => 1459.00,
            'quantity' => 1,
            'type_contrat' => 'achat',
            'status' => 'livré',
            'commande_status' => 'active',
            'fournisseur' => 'Samsung',
            'etat' => 'Neuf',
        ]);

        // Commande 3 : Licences Microsoft 365
        $commande3 = Commande::create([
            'reference_commande' => 'CMD-2024-003',
            'entreprise_id' => $entreprise->id,
            'signataire_id' => $signataire->id,
            'user_id' => $admin->id,
            'statut' => 'validée',
            'financeur' => 'location',
            'date_debut_contrat' => Carbon::now()->subMonths(8),
            'date_fin_contrat' => Carbon::now()->addMonths(4),
            'date_validation' => Carbon::now()->subMonths(8),
        ]);

        // Attribuer Microsoft 365 à tous les utilisateurs
        foreach ([$admin, $jean, $marie, $sophie, $thomas, $camille, $julien, $laura, $nicolas] as $user) {
            CommandeProduct::create([
                'commande_id' => $commande3->id,
                'entreprise_id' => $entreprise->id,
                'user_attributed_id' => $user->id,
                'name' => $office365->name,
                'slug' => $office365->slug,
                'categorie' => $office365->categorie,
                'sous_categorie' => $office365->sous_categorie,
                'marque' => $office365->marque,
                'type_licence' => $office365->type_licence,
                'image_principale' => '/images/microsoft-logo.png',
                'prix' => 10.50,
                'quantity' => 1,
                'type_contrat' => 'location',
                'status' => 'active',
                'commande_status' => 'active',
                'fournisseur' => 'Microsoft',
                'date_debut_licence' => Carbon::now()->subMonths(8),
                'date_fin_licence' => Carbon::now()->addMonths(4),
                'auto_renew' => true,
            ]);
        }

        // Commande 4 : Accessoires divers (moitié en location, moitié en achat ce mois)
        $commande4 = Commande::create([
            'reference_commande' => 'CMD-2024-004',
            'entreprise_id' => $entreprise->id,
            'signataire_id' => $signataire->id,
            'user_id' => $admin->id,
            'statut' => 'validée',
            'financeur' => 'achat',
            'date_debut_contrat' => Carbon::now(),
            'date_validation' => Carbon::now(),
        ]);

        // Commande 5 : Claviers en location
        $commande5 = Commande::create([
            'reference_commande' => 'CMD-2024-005',
            'entreprise_id' => $entreprise->id,
            'signataire_id' => $signataire->id,
            'user_id' => $admin->id,
            'statut' => 'validée',
            'financeur' => 'location',
            'date_debut_contrat' => Carbon::now()->subMonths(6),
            'date_fin_contrat' => Carbon::now()->addMonths(30),
            'date_validation' => Carbon::now()->subMonths(6),
        ]);

        // Écrans pour certains utilisateurs
        foreach ([$jean, $thomas, $nicolas, $sophie] as $user) {
            CommandeProduct::create([
                'commande_id' => $commande4->id,
                'entreprise_id' => $entreprise->id,
                'user_attributed_id' => $user->id,
                'adresse_livraison_id' => $adresse->id,
                'name' => $dellMonitor->name,
                'slug' => $dellMonitor->slug,
                'categorie' => $dellMonitor->categorie,
                'sous_categorie' => $dellMonitor->sous_categorie,
                'marque' => $dellMonitor->marque,
                'modele' => $dellMonitor->modele,
                'taille_ecran' => $dellMonitor->taille_ecran,
                'resolution_ecran' => $dellMonitor->resolution_ecran,
                'image_principale' => '/images/created_ecrans_icon.svg',
                'prix' => 549.00,
                'quantity' => 1,
                'type_contrat' => 'achat',
                'status' => 'livré',
                'commande_status' => 'active',
                'fournisseur' => 'Dell',
                'etat' => 'Neuf',
            ]);
        }

        // Claviers et souris pour les développeurs
        foreach ([$jean, $thomas, $nicolas] as $user) {
            // Clavier en location
            CommandeProduct::create([
                'commande_id' => $commande5->id,
                'entreprise_id' => $entreprise->id,
                'user_attributed_id' => $user->id,
                'adresse_livraison_id' => $adresse->id,
                'name' => $logitechKeyboard->name,
                'slug' => $logitechKeyboard->slug,
                'categorie' => $logitechKeyboard->categorie,
                'sous_categorie' => $logitechKeyboard->sous_categorie,
                'marque' => $logitechKeyboard->marque,
                'image_principale' => '/images/created_claviers_icon.svg',
                'prix' => 3.31,
                'quantity' => 1,
                'type_contrat' => 'location',
                'status' => 'livré',
                'commande_status' => 'active',
                'fournisseur' => 'Logitech',
                'etat' => 'Neuf',
            ]);

            // Souris en achat ce mois
            CommandeProduct::create([
                'commande_id' => $commande4->id,
                'entreprise_id' => $entreprise->id,
                'user_attributed_id' => $user->id,
                'adresse_livraison_id' => $adresse->id,
                'name' => $logitechMouse->name,
                'slug' => $logitechMouse->slug,
                'categorie' => $logitechMouse->categorie,
                'sous_categorie' => $logitechMouse->sous_categorie,
                'marque' => $logitechMouse->marque,
                'image_principale' => '/images/created_souris_icon.svg',
                'prix' => 109.00,
                'quantity' => 1,
                'type_contrat' => 'achat',
                'status' => 'livré',
                'commande_status' => 'active',
                'fournisseur' => 'Logitech',
                'etat' => 'Neuf',
            ]);
        }

        // Casques pour toute l'équipe
        foreach ([$admin, $jean, $marie, $thomas, $julien, $laura] as $user) {
            CommandeProduct::create([
                'commande_id' => $commande4->id,
                'entreprise_id' => $entreprise->id,
                'user_attributed_id' => $user->id,
                'adresse_livraison_id' => $adresse->id,
                'name' => $jblHeadset->name,
                'slug' => $jblHeadset->slug,
                'categorie' => $jblHeadset->categorie,
                'sous_categorie' => $jblHeadset->sous_categorie,
                'marque' => $jblHeadset->marque,
                'image_principale' => '/images/created_casques-micros_icon.svg',
                'prix' => 239.00,
                'quantity' => 1,
                'type_contrat' => 'achat',
                'status' => 'livré',
                'commande_status' => 'active',
                'fournisseur' => 'Jabra',
                'etat' => 'Neuf',
            ]);
        }

        // Récupération des équipements pour les tickets de support
        $macbookEquipement = CommandeProduct::where('entreprise_id', $entreprise->id)
            ->where('user_attributed_id', $jean->id)
            ->where('categorie', 'Ordinateur')
            ->first();

        $iphoneEquipement = CommandeProduct::where('entreprise_id', $entreprise->id)
            ->where('user_attributed_id', $marie->id)
            ->where('categorie', 'Téléphonie')
            ->first();

        // Création de tickets de support
        $support1 = Support::create([
            'numero_support' => 'SUP-2024-001',
            'object' => 'Problème de démarrage du MacBook Pro',
            'status' => 'En cours',
            'commande_id' => $commande1->id,
            'equipement_id' => $macbookEquipement?->id,
            'user_id' => $jean->id,
            'entreprise_id' => $entreprise->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        SupportMessage::create([
            'message' => 'Bonjour, mon MacBook Pro ne démarre plus depuis ce matin. L\'écran reste noir même après avoir appuyé sur le bouton power pendant plusieurs secondes. La batterie était chargée à 80% hier soir.',
            'from' => 'user',
            'user_id' => $jean->id,
            'support_id' => $support1->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        SupportMessage::create([
            'message' => 'Bonjour Jean, merci pour votre retour. Pouvez-vous essayer de maintenir les touches Command + Option + P + R enfoncées pendant 20 secondes au démarrage ? Cela réinitialisera la NVRAM.',
            'from' => 'admin',
            'support_id' => $support1->id,
            'created_at' => Carbon::now()->subDays(1)->subHours(3),
        ]);

        SupportMessage::create([
            'message' => 'J\'ai essayé la manipulation mais le problème persiste. L\'écran reste toujours noir.',
            'from' => 'user',
            'user_id' => $jean->id,
            'support_id' => $support1->id,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        $support2 = Support::create([
            'numero_support' => 'SUP-2024-002',
            'object' => 'Demande d\'assistance pour configuration Teams',
            'status' => 'Résolu',
            'user_id' => $marie->id,
            'entreprise_id' => $entreprise->id,
            'created_at' => Carbon::now()->subDays(5),
        ]);

        SupportMessage::create([
            'message' => 'Bonjour, j\'ai besoin d\'aide pour configurer Microsoft Teams sur mon iPhone. Je n\'arrive pas à activer les notifications de réunion.',
            'from' => 'user',
            'user_id' => $marie->id,
            'support_id' => $support2->id,
            'created_at' => Carbon::now()->subDays(5),
        ]);

        SupportMessage::create([
            'message' => 'Bonjour Marie, il faut aller dans Réglages > Notifications > Teams et activer "Autoriser les notifications". Ensuite, dans l\'app Teams, allez dans Paramètres > Notifications et activez les notifications de réunion.',
            'from' => 'admin',
            'support_id' => $support2->id,
            'created_at' => Carbon::now()->subDays(5)->addHours(2),
        ]);

        SupportMessage::create([
            'message' => 'Parfait, ça fonctionne maintenant ! Merci beaucoup pour votre aide.',
            'from' => 'user',
            'user_id' => $marie->id,
            'support_id' => $support2->id,
            'created_at' => Carbon::now()->subDays(4),
        ]);

        // Affichage d'un message de confirmation
        $this->command->info('✅ Base de données alimentée avec succès !');
        $this->command->info('');
        $this->command->info('🏢 Entreprise créée : ' . $entreprise->raison_sociale);
        $this->command->info('👤 Utilisateur admin créé :');
        $this->command->info('   Email: admin@manystacks.co');
        $this->command->info('   Mot de passe: password');
        $this->command->info('');
        $this->command->info('📦 ' . Product::count() . ' produits créés');
        $this->command->info('👥 ' . User::count() . ' collaborateurs créés');
        $this->command->info('📍 ' . AdresseLivraison::count() . ' adresses de livraison créées');
        $this->command->info('🛒 ' . Commande::count() . ' commandes créées');
        $this->command->info('📱 ' . CommandeProduct::count() . ' équipements/licences attribués');
        $this->command->info('🎫 ' . Support::count() . ' tickets de support créés');
    }
}
