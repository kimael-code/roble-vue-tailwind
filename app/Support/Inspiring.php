<?php

namespace App\Support;

use Illuminate\Foundation\Inspiring as IlluminateInspiring;
use Illuminate\Support\Collection;

class Inspiring extends IlluminateInspiring
{
    public static function quotes()
    {
        if (app()->getLocale() != 'es_VE')
        {
            return parent::quotes();
        }

        return new Collection([
            'Sé el cambio que quieres ver en el mundo. - Mahatma Gandhi',
            'No se trata de si van a derribarte, se trata de si vas a levantarte cuando lo hagan. - Vince Lombardi, entrenador de fúbol americano',
            'Nadie puede hacerte sentir inferior sin tu consentimiento. - Eleanor Roosevelt',
            'Qué maravilloso es que nadie tenga que esperar ni un segundo para empezar a mejorar el mundo. - Ana Frank',
            'El pesimista ve dificultades en cada oportunidad. El optimista ve oportunidades en cada dificultad. - Winston Churchill',
            'Muchos piensan en cambiar el mundo, pero casi nadie piensa en cambiarse a sí mismo. - Leon Tolstoi',
            'Si estás trabajando en algo que te importa de verdad, nadie tiene que empujarte: tu visión te empuja. - Steve Jobs',
            'No tienes que ser grande para empezar. Pero tienes que empezar para poder ser grande. - Zig Ziglar',
            'Podemos sufrir muchas derrotas pero no debemos ser derrotados. - Maya Angelou',
            'El momento en que quieres dejarlo es justo el momento en que tienes que seguir avanzando. - Anónimo',
            'No esperes. Nunca va a ser el momento adecuado. - Napoleon Hill',
            'La creatividad es la inteligencia divirtiéndose. - Albert Einstein',
            'Rodéate de personas que crean en tus sueños, animen tus ideas, apoyen tus ambiciones, y saquen lo mejor de ti. - Roy T. Bennet',
            'No importa lo que te diga la gente, las palabras y las ideas pueden cambiar el mundo. - Robin Williams',
            'Nunca eres demasiado viejo para marcarte otra meta o tener un nuevo sueño. - C.S. Lewis',
            'No te pliegues. No lo diluyas. No intentes hacerlo lógico. No adaptes tu propia alma a las costumbres de los demás. En lugar de todo eso, sigue tu propia obsesión implacablemente. - Franz Kafka',
            'La mayor parte de las grandes cosas que ha conseguido el hombre, fueron declaradas imposibles antes de que alguien las hiciera. - Louis D. Brandeis',
            'La verdadera motivación procede de trabajar en cosas que nos importan. - Sheryl Sandberg',
            'La energía y la persistencia conquistan todas las cosas. - Benjamin Franklin',
            'Sé amable, porque toda persona que conoces está librando una gran batalla. - Platón',
            'La lógica te llevará de la A a la Z. La imaginación te llevará a cualquier lugar. - Albert Einstein',
            'Las ideas no duran mucho. Hay que hacer algo con ellas. - Santiago Ramón y Cajal',
            'Te deseo que estés vivo cada día de tu vida. - Jonathan Swift',
            'Sé tú mismo. Todos los demás ya están ocupados. - Oscar Wilde.',
            'Somos lo que hacemos de forma repetida. La excelencia no es un acto, sino un hábito - Aristóteles',
            'Cuando te levantes por la mañana piensa en qué privilegio es estar vivo, pensar, disfrutar, amar - Marco Aurelio',
            'Tu tiempo es limitado, no lo malgastes viviendo la vida de otro. No te dejes atrapar por el dogma - Steve Jobs',
            'Empieza haciendo lo que es necesario; después haz lo que es posible; y pronto te encontrarás haciendo lo imposible - San Francisco de Asís',
            'El arte de vencer se aprende en las derrotas - Simón Bolívar',
            'El mayor logro es ser tú mismo en un mundo que continuamente trata de convertirte en otra cosa - Ralph Waldo Emerson',
        ]);
    }
}
