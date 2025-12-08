<?php

namespace App\Controllers;

class PagesController extends BaseController
{
    /**
     * Page À propos
     */
    public function about()
    {
        $data = [
            'title' => 'À propos de Pilom',
            'description' => 'Découvrez Pilom, la solution de gestion pour entrepreneurs'
        ];
        
        return view('pages/about', $data);
    }

    /**
     * Page Contact
     */
    public function contact()
    {
        $data = [
            'title' => 'Contactez-nous',
            'description' => 'Contactez l\'équipe Pilom pour toute question'
        ];

        // Handle form submission
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[2]|max_length[100]',
                'email' => 'required|valid_email',
                'subject' => 'required|min_length[5]|max_length[200]',
                'message' => 'required|min_length[10]|max_length[2000]'
            ];

            if (!$this->validate($rules)) {
                return view('pages/contact', array_merge($data, [
                    'validation' => $this->validator
                ]));
            }

            // TODO: Send email or store in database
            // For now, just show success message
            return redirect()->to('/contact')->with('success', 'Votre message a été envoyé. Nous vous répondrons rapidement.');
        }
        
        return view('pages/contact', $data);
    }

    /**
     * Page FAQ
     */
    public function faq()
    {
        $data = [
            'title' => 'Questions Fréquentes',
            'description' => 'Trouvez les réponses à vos questions sur Pilom',
            'faqs' => [
                [
                    'question' => 'Qu\'est-ce que Pilom ?',
                    'answer' => 'Pilom est une solution de gestion complète pour les entrepreneurs et petites entreprises. Elle permet de gérer la facturation, les devis, les clients, la trésorerie et bien plus encore.'
                ],
                [
                    'question' => 'Comment créer une facture ?',
                    'answer' => 'Connectez-vous à votre espace, allez dans la section "Factures" et cliquez sur "Nouvelle facture". Remplissez les informations client et les lignes de facturation, puis enregistrez ou envoyez directement.'
                ],
                [
                    'question' => 'Puis-je personnaliser mes documents ?',
                    'answer' => 'Oui, vous pouvez personnaliser vos factures et devis avec votre logo, vos couleurs et vos mentions légales depuis les paramètres de votre compte.'
                ],
                [
                    'question' => 'Mes données sont-elles sécurisées ?',
                    'answer' => 'Oui, nous utilisons un chiffrement de bout en bout et vos données sont hébergées sur des serveurs sécurisés en France, conformément au RGPD.'
                ],
                [
                    'question' => 'Comment contacter le support ?',
                    'answer' => 'Vous pouvez nous contacter via la page Contact ou par email à support@pilom.fr. Notre équipe répond généralement sous 24h ouvrées.'
                ],
                [
                    'question' => 'Y a-t-il une période d\'essai gratuite ?',
                    'answer' => 'Oui, nous offrons une période d\'essai gratuite de 14 jours sans engagement et sans carte bancaire requise.'
                ]
            ]
        ];
        
        return view('pages/faq', $data);
    }

    /**
     * Mentions légales
     */
    public function legal()
    {
        $data = [
            'title' => 'Mentions Légales',
            'description' => 'Mentions légales de Pilom'
        ];
        
        return view('pages/legal', $data);
    }

    /**
     * Conditions Générales d'Utilisation
     */
    public function terms()
    {
        $data = [
            'title' => 'Conditions Générales d\'Utilisation',
            'description' => 'CGU de Pilom'
        ];
        
        return view('pages/terms', $data);
    }

    /**
     * Politique de Confidentialité / CGV
     */
    public function privacy()
    {
        $data = [
            'title' => 'Politique de Confidentialité',
            'description' => 'Politique de confidentialité et protection des données de Pilom'
        ];
        
        return view('pages/privacy', $data);
    }
}
