<?php
namespace AIPSTX;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (!class_exists('\\AIPSTX\\AIPSTX_img_artists')) {
    class AIPSTX_img_artists {
        private static $instance = null;
        public $aipstx_artists = [
            'Leonardo da Vinci' => 'Leonardo da Vinci',
            'Michelangelo' => 'Michelangelo',
            'Vincent van Gogh' => 'Vincent van Gogh',
            'Pablo Picasso' => 'Pablo Picasso',
            'Rembrandt' => 'Rembrandt',
            'Claude Monet' => 'Claude Monet',
            'Salvador Dalí' => 'Salvador Dalí',
            'Andy Warhol' => 'Andy Warhol',
            'Jackson Pollock' => 'Jackson Pollock',
            'Raphael' => 'Raphael',
            'Gustav Klimt' => 'Gustav Klimt',
            'Henri Matisse' => 'Henri Matisse',
            'Sandro Botticelli' => 'Sandro Botticelli',
            'Caravaggio' => 'Caravaggio',
            'Peter Paul Rubens' => 'Peter Paul Rubens',
            'Diego Velázquez' => 'Diego Velázquez',
            'Johannes Vermeer' => 'Johannes Vermeer',
            'Edgar Degas' => 'Edgar Degas',
            'Titian' => 'Titian',
            'Paul Cézanne' => 'Paul Cézanne',
            'René Magritte' => 'René Magritte',
            'Paul Gauguin' => 'Paul Gauguin',
            'Francisco Goya' => 'Francisco Goya',
            'Albrecht Dürer' => 'Albrecht Dürer',
            'William Turner' => 'William Turner',
            'Frida Kahlo' => 'Frida Kahlo',
            'Pierre-Auguste Renoir' => 'Pierre-Auguste Renoir',
            'Jan van Eyck' => 'Jan van Eyck',
            'Edward Hopper' => 'Edward Hopper',
            'Kazimir Malevich' => 'Kazimir Malevich',
            'Wassily Kandinsky' => 'Wassily Kandinsky',
            'Edvard Munch' => 'Edvard Munch',
            'El Greco' => 'El Greco',
            'Marc Chagall' => 'Marc Chagall',
            'Georges Seurat' => 'Georges Seurat',
            'Gustave Courbet' => 'Gustave Courbet',
            'Hieronymus Bosch' => 'Hieronymus Bosch',
            'Tintoretto' => 'Tintoretto',
            'Jean-Michel Basquiat' => 'Jean-Michel Basquiat',
            'Henri de Toulouse-Lautrec' => 'Henri de Toulouse-Lautrec',
            'Artemisia Gentileschi' => 'Artemisia Gentileschi',
            'Amedeo Modigliani' => 'Amedeo Modigliani',
            'Giorgio de Chirico' => 'Giorgio de Chirico',
            'Paul Klee' => 'Paul Klee',
            'John Singer Sargent' => 'John Singer Sargent',
            'Piet Mondrian' => 'Piet Mondrian',
            'Jasper Johns' => 'Jasper Johns',
            'Joan Miró' => 'Joan Miró',
            'Mary Cassatt' => 'Mary Cassatt',
            'Fernando Botero' => 'Fernando Botero',
            'Marcel Duchamp' => 'Marcel Duchamp',
            'Yayoi Kusama' => 'Yayoi Kusama',
            'Pieter Bruegel the Elder' => 'Pieter Bruegel the Elder',
            'Camille Pissarro' => 'Camille Pissarro',
            'Winslow Homer' => 'Winslow Homer',
            'Willem de Kooning' => 'Willem de Kooning',
            'Roy Lichtenstein' => 'Roy Lichtenstein',
            'Eugene Delacroix' => 'Eugene Delacroix',
            'M. C. Escher' => 'M. C. Escher',
            'Alfred Sisley' => 'Alfred Sisley',
            'Caspar David Friedrich' => 'Caspar David Friedrich',
            'Andrea Mantegna' => 'Andrea Mantegna',
        ];

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
        }

        public function aipstx_get_artists_from_post() {
            // Nonce kontrolü

            if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {

                wp_send_json_error(array('message' => 'Nonce verification failed.'));

                return;

            }
            $aipstx_artist = isset($_POST['artist']) ? sanitize_text_field($_POST['artist']) : '';
            return $aipstx_artist;
        }

        public function aipstx_get_artists() {
            return $this->aipstx_artists;
        }
    }

    AIPSTX_img_artists::get_instance();
}
