<?php

namespace Database\Seeders;

use App\Enums\LessonLevelEnum;
use App\Models\Category;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $lessons = [
            [
                "title" => "Dorothy’s Small House in Kansas",
                "description" => "Dorothy lives in Kansas. She lives with Uncle Henry and Aunt Em",
                "text" => "Dorothy lives in Kansas. She lives with Uncle Henry and Aunt Em. Uncle Henry is a farmer. Aunt Em is his wife.\n\nTheir house is small. The wood for the house comes from far away. The house has one room.\n\nIn the room, there is a stove, a table, some chairs, and beds. Uncle Henry and Aunt Em have a big bed. Dorothy has a small bed.\n\nThere is no attic. There is no basement.\n\nThere is a small hole in the ground. It is called a cyclone cellar. The family goes there when there is a big storm.\n\nThere is a door in the floor. There is a ladder. The ladder goes down into the dark hole.",
                "translation" => "Dorothy vive no Kansas. Ela vive com o Tio Henry e a Tia Em. O Tio Henry é fazendeiro. A Tia Em é sua esposa.\n\nA casa deles é pequena. A madeira para a casa vem de longe. A casa tem apenas um cômodo.\n\nNo cômodo, há um fogão, uma mesa, algumas cadeiras e camas. O Tio Henry e a Tia Em têm uma cama grande. Dorothy tem uma cama pequena.\n\nNão há sótão. Não há porão.\n\nHá um pequeno buraco no chão. Ele é chamado de abrigo contra ciclone. A família vai para lá quando há uma grande tempestade.\n\nHá uma porta no chão. Há uma escada. A escada desce para o buraco escuro.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/Dorothy_s%20Small%20House%20in%20Kansas.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/Dorothy_s%20Small%20House%20in%20Kansas.mp3",
                "duration" => 42,
                "level" => LessonLevelEnum::Beginner,
                "category_id" => 1,
                "source" => "The Wonderful Wizard of Oz",
                "is_free" => true
            ],
            [
                "title" => "My First Home as a Horse",
                "description" => "The first place I remember is a big, nice field",
                "text" => "The first place I remember is a big, nice field. There is a pond with clear water. Some trees give shade. There are plants in the water.\n\nOn one side, there is a field with lines in the soil. On the other side, there is a gate. We can see the master’s house near the road. At the top of the field, there are many trees. At the bottom, there is a small river.\n\nWhen I was young, I drank my mother’s milk. I could not eat grass.\n\nIn the day, I stayed near my mother. At night, I slept next to her.\n\nWhen it was hot, we stood near the pond in the shade. When it was cold, we stayed in a warm place near the trees.\n\nWhen I was big enough, I started to eat grass. My mother went to work in the day. She came back in the evening.",
                "translation" => "O primeiro lugar de que me lembro é um campo grande e agradável. Há um lago com água limpa. Algumas árvores fazem sombra. Há plantas na água.\n\nDe um lado, há um campo com a terra arada. Do outro lado, há um portão. Podemos ver a casa do nosso dono perto da estrada. Na parte de cima do campo, há muitas árvores. Na parte de baixo, há um pequeno rio.\n\nQuando eu era jovem, eu bebia o leite da minha mãe. Eu não podia comer grama.\n\nDurante o dia, eu ficava perto da minha mãe. À noite, eu dormia ao lado dela.\n\nQuando fazia calor, nós ficávamos perto do lago, na sombra. Quando fazia frio, nós ficávamos em um lugar quente perto das árvores.\n\nQuando eu fiquei grande o suficiente, comecei a comer grama. Minha mãe saía para trabalhar durante o dia. Ela voltava à noite.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/My%20First%20Home%20as%20a%20Horse.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/My%20First%20Home%20as%20a%20Horse.mp3",
                "duration" => 51,
                "level" => LessonLevelEnum::Beginner,
                "category_id" => 1,
                "source" => "Black Beauty",
                "is_free" => false
            ],
            [
                "title" => "White Rabbit",
                "description" => "Alice is sitting next to her sister. She feels very tired.",
                "text" => "Alice is sitting next to her sister. She feels very tired. She has nothing to do.\n\nShe looks at her sister’s book one or two times. But the book has no pictures and no talking.\n\nAlice thinks, “What is the use of a book without pictures or talking?”\n\nShe thinks about making a chain with flowers. She wants to know if it is worth getting up to pick the flowers.\n\nSuddenly, a White Rabbit with pink eyes runs near her.",
                "translation" => "Alice está sentada ao lado de sua irmã. Ela se sente muito cansada. Ela não tem nada para fazer.\n\nEla olha o livro de sua irmã uma ou duas vezes. Mas o livro não tem figuras e não tem conversas.\n\nAlice pensa: “Qual é a utilidade de um livro sem figuras ou conversas?”\n\nEla pensa em fazer uma corrente de flores. Ela quer saber se vale a pena levantar para pegar as flores.\n\nDe repente, um Coelho Branco com olhos cor-de-rosa corre perto dela.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/White%20Rabbit.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/White%20Rabbit.mp3",
                "duration" => 30,
                "level" => LessonLevelEnum::Beginner,
                "category_id" => 1,
                "source" => "Alice's Adventures in Wonderland",
                "is_free" => false
            ],
            [
                "title" => "The Dashwood Family at Norland Park",
                "description" => "The Dashwood family had lived in Sussex for a long time.",
                "text" => "The Dashwood family had lived in Sussex for a long time. They owned a large estate, and their home was at Norland Park, in the middle of their land. Many generations of the family had lived there and were well respected by their neighbors.\n\nThe last owner of the estate was an old man who lived to a very old age. For many years, he lived with his sister, who took care of his house. When she died ten years before him, the house became different.\n\nAfter her death, he invited his nephew, Mr. Henry Dashwood, to live with him. Mr. Dashwood was the legal heir of the estate and was expected to inherit it in the future. He moved in with his wife and children.\n\nThe old man enjoyed living with them. He became very attached to the family. Mr. and Mrs. Dashwood took good care of him, not only because of the inheritance, but also because they were kind. Their children also made him happy in his old age.",
                "translation" => "A família Dashwood vivia em Sussex há muito tempo. Eles possuíam uma grande propriedade, e sua casa ficava em Norland Park, no centro de suas terras. Muitas gerações da família haviam vivido ali e eram muito respeitadas pelos seus vizinhos.\n\nO último dono da propriedade era um homem idoso que viveu até uma idade muito avançada. Por muitos anos, ele viveu com sua irmã, que cuidava de sua casa. Quando ela morreu dez anos antes dele, a casa mudou.\n\nApós sua morte, ele convidou seu sobrinho, Sr. Henry Dashwood, para viver com ele. O Sr. Dashwood era o herdeiro legal da propriedade e deveria herdá-la no futuro. Ele se mudou com sua esposa e filhos.\n\nO velho gostava de viver com eles. Ele se apegou muito à família. O Sr. e a Sra. Dashwood cuidavam bem dele, não apenas por causa da herança, mas também porque eram bondosos. Os filhos deles também o faziam feliz em sua velhice.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/The%20Dashwood%20Family.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/The%20Dashwood%20Family.mp3",
                "duration" => 53,
                "level" => LessonLevelEnum::Intermediate,
                "category_id" => 1,
                "source" => "Sense and Sensibility",
                "is_free" => true
            ],
            [
                "title" => "A Quiet Escape",
                "description" => "On an exceptionally hot evening early in July...",
                "text" => "On an exceptionally hot evening early in July, a young man came out of the garret in which he lodged in S. Place and walked slowly, as though in hesitation, towards K. bridge.\n\nHe had successfully avoided meeting his landlady on the staircase.\n\nHis garret was under the roof of a high, five-storied house and was more like a cupboard than a room.\n\nThe landlady who provided him with garret, dinners, and attendance, lived on the floor below, and every time he went out he was obliged to pass her kitchen, the door of which invariably stood open.\n\nAnd each time he passed, the young man had a sick, frightened feeling, which made him scowl and feel ashamed.\n\nHe was hopelessly in debt to his landlady, and was afraid of meeting her.",
                "translation" => "Numa noite excepcionalmente quente no início de julho, um jovem saiu do quarto no sótão em que morava, na S. Place, e caminhou lentamente, como se hesitasse, em direção à ponte K.\n\nEle havia conseguido evitar encontrar sua senhoria na escada.\n\nSeu quarto ficava sob o teto de uma alta casa de cinco andares e era mais parecido com um armário do que com um cômodo.\n\nA senhoria, que lhe fornecia o quarto, as refeições e os cuidados, morava no andar de baixo, e toda vez que ele saía era obrigado a passar pela cozinha dela, cuja porta invariavelmente ficava aberta.\n\nE cada vez que passava, o jovem sentia um mal-estar doentio e amedrontado, que o fazia franzir o cenho e sentir-se envergonhado.\n\nEle estava irremediavelmente endividado com sua senhoria e tinha medo de encontrá-la.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/A%20Quiet%20Escape1.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/A%20Quiet%20Escape.mp3",
                "duration" => 40,
                "level" => LessonLevelEnum::Advanced,
                "category_id" => 1,
                "source" => "Crime and Punishment",
                "is_free" => false
            ],
            [
                "title" => "Call me Ishmael",
                "description" => "Some years ago—never mind how long precisely...",
                "text" => "Call me Ishmael.\n\nSome years ago, never mind how long precisely, having little or no money in my purse, and nothing particular to interest me on shore, I thought I would sail about a little and see the watery part of the world.\n\nIt is a way I have of driving off the spleen and regulating the circulation.\n\nWhenever I find myself growing grim about the mouth; whenever it is a damp, drizzly November in my soul; whenever I find myself involuntarily pausing before coffin warehouses, and bringing up the rear of every funeral I meet;\n\nand especially whenever my hypos get such an upper hand of me, that it requires a strong moral principle to prevent me from deliberately stepping into the street, and methodically knocking people’s hats off—\n\nthen, I account it high time to get to sea as soon as I can.\n\nThis is my substitute for pistol and ball.\n\nWith a philosophical flourish Cato throws himself upon his sword; I quietly take to the ship.\n\nThere is nothing surprising in this.\n\nIf they but knew it, almost all men in their degree, some time or other, cherish very nearly the same feelings towards the ocean with me.",
                "translation" => "Chame-me Ishmael.\n\nHá alguns anos, não importa exatamente quanto tempo, tendo pouco ou nenhum dinheiro na bolsa, e nada em particular que me interessasse em terra, pensei que navegaria um pouco e veria a parte aquática do mundo.\n\nÉ um modo que tenho de afastar a melancolia e regular a circulação.\n\nSempre que me vejo ficando carrancudo ao redor da boca; sempre que é um novembro úmido e chuvoso em minha alma; sempre que me pego involuntariamente parando diante de armazéns de caixões e seguindo atrás de todos os funerais que encontro;\n\ne especialmente sempre que meus humores sombrios assumem tal domínio sobre mim, que é necessário um forte princípio moral para impedir-me de deliberadamente entrar na rua e metodicamente derrubar os chapéus das pessoas—\n\nentão considero que é mais do que hora de ir para o mar o mais rápido possível.\n\nEste é o meu substituto para pistola e bala.\n\nCom um floreio filosófico, Catão lança-se sobre sua espada; eu, tranquilamente, tomo o navio.\n\nNão há nada de surpreendente nisso.\n\nSe apenas o soubessem, quase todos os homens, em algum grau, em um momento ou outro, nutrem sentimentos muito semelhantes aos meus em relação ao oceano.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/Call%20me%20Ishmael1.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/Call%20me%20Ishmael.mp3",
                "duration" => 57,
                "level" => LessonLevelEnum::Advanced,
                "category_id" => 1,
                "source" => "Mobby Dick",
                "is_free" => true
            ],
            [
                "title" => "Metamorphosis",
                "description" => "One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin.",
                "text" => "One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin.\n\nHe lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff sections. The bedding was hardly able to cover it and seemed ready to slide off any moment.\n\nHis many legs, pitifully thin compared with the size of the rest of him, waved about helplessly as he looked. “What’s happened to me?” he thought. It wasn’t a dream.\n\nHis room, a proper human room although a little too small, lay peacefully between its four familiar walls. A collection of textile samples lay spread out on the table, Samsa was a travelling salesman, and above it there hung a picture that he had recently cut out of an illustrated magazine and housed in a nice, gilded frame.\n\nIt showed a lady fitted out with a fur hat and fur boa who sat upright, raising a heavy fur muff that covered the whole of her lower arm towards the viewer.",
                "translation" => "Uma manhã, quando Gregor Samsa acordou de sonhos inquietos, encontrou-se transformado em sua cama em um horrível inseto.\n\nEle jazia sobre o dorso duro como uma armadura e, ao erguer um pouco a cabeça, podia ver seu ventre marrom, levemente abaulado e dividido por arcos em rígidas seções. A roupa de cama mal conseguia cobri-lo e parecia prestes a escorregar a qualquer momento.\n\nSuas muitas pernas, lamentavelmente finas em comparação com o tamanho do resto do corpo, agitavam-se desamparadas diante de seus olhos. “O que aconteceu comigo?”, pensou. Não era um sonho.\n\nSeu quarto, um quarto humano comum, embora um pouco pequeno demais, permanecia tranquilamente entre suas quatro paredes familiares. Uma coleção de amostras de tecidos estava espalhada sobre a mesa, Samsa era um caixeiro-viajante, e, acima dela, pendia um quadro que ele havia recortado recentemente de uma revista ilustrada e colocado em uma bonita moldura dourada.\n\nEle mostrava uma dama vestida com um chapéu de pele e uma estola de pele, sentada ereta, erguendo em direção ao observador um pesado regalo de pele que cobria todo o seu antebraço.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/Metamorphosis.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/Metamorphosis.mp3",
                "duration" => 52,
                "level" => LessonLevelEnum::Advanced,
                "category_id" => 1,
                "source" => "Metamorphosis",
                "is_free" => false
            ],
            [
                "title" => "Leaving Norland",
                "description" => "Mrs. Dashwood stayed at Norland for a few months. At first, every place there made her very sad. But after some time, she felt a little better and wanted to leave.",
                "text" => "Mrs. Dashwood stayed at Norland for a few months. At first, every place there made her very sad. But after some time, she felt a little better and wanted to leave. \n\nShe tried hard to find a new house near Norland, because she did not want to go far away from a place she loved.\n\nHowever, she could not find a house that was both comfortable and not too expensive. \n\nHer eldest daughter was more careful and sensible. She said no to many houses because they were too big and cost too much money, even though her mother liked them.",
                "translation" => "A Sra. Dashwood ficou em Norland por alguns meses. No começo, cada lugar ali a deixava muito triste. Mas depois de algum tempo, ela se sentiu um pouco melhor e quis ir embora. \n\nEla se esforçou para encontrar uma nova casa perto de Norland, porque não queria ficar longe de um lugar que amava.\n\nNo entanto, ela não conseguiu encontrar uma casa que fosse confortável e não fosse muito cara. \n\nSua filha mais velha era mais cuidadosa e sensata. Ela recusou muitas casas porque eram grandes demais e custavam muito dinheiro, mesmo que sua mãe gostasse delas.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/Leaving%20Norland.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/Leaving%20Norland.mp3",
                "duration" => 33,
                "level" => LessonLevelEnum::Beginner,
                "category_id" => 1,
                "source" => "Sense and Sensibility",
                "is_free" => false
            ],
            [
                "title" => "A Chance Meeting on a Foggy Train",
                "description" => "At the end of November, during a warmer day, a train on the Warsaw and Petersburg railway was getting close to the city at high speed. The morning was very damp and foggy",
                "text" => "At the end of November, during a warmer day, a train on the Warsaw and Petersburg railway was getting close to the city at high speed. The morning was very damp and foggy, and it was hard for the daylight to appear. People could not see more than a few meters from the train windows.\n\nSome passengers on the train were coming back from other countries. However, the third-class carriages were the most crowded. They were filled with ordinary people of different jobs and backgrounds who had entered the train at stations near the city. Everyone looked tired. Many had sleepy eyes and were cold. Their faces even seemed to have the same pale color as the fog outside.\n\nWhen the day became a little brighter, two passengers in one of the third-class carriages were sitting across from each other. Both were young men, and both wore simple, worn clothes. They had interesting faces, and it was clear that both wanted to start a conversation.\n\nIf they had known how unusual they really were at that moment, they would probably have been surprised by the strange chance that made them sit across from each other in that third-class carriage.",
                "translation" => "No final de novembro, durante um dia mais quente, um trem da ferrovia Varsóvia–Petersburgo estava se aproximando da cidade em alta velocidade. A manhã estava muito úmida e com neblina, e era difícil a luz do dia aparecer. As pessoas não conseguiam ver mais do que alguns metros pelas janelas do trem.\n\nAlguns passageiros estavam voltando de outros países. No entanto, os vagões de terceira classe estavam os mais cheios. Eles estavam ocupados por pessoas comuns, de diferentes profissões e origens, que haviam embarcado nas estações mais próximas da cidade. Todos pareciam cansados. Muitos tinham olhos sonolentos e estavam com frio. Seus rostos até pareciam ter a mesma cor pálida da neblina do lado de fora.\n\nQuando o dia ficou um pouco mais claro, dois passageiros em um dos vagões de terceira classe estavam sentados um de frente para o outro. Ambos eram jovens, e ambos usavam roupas simples e gastas. Eles tinham rostos interessantes, e era claro que os dois queriam começar uma conversa.\n\nSe soubessem o quão incomuns realmente eram naquele momento, provavelmente ficariam surpresos com o estranho acaso que os fez sentar um de frente para o outro naquele vagão de terceira classe.",
                "image_url" => "https://ik.imagekit.io/9azlerk2s/A%20Chance%20Meeting%20on%20a%20Foggy%20Train.png",
                "audio_url" => "https://ik.imagekit.io/9azlerk2s/A%20Chance%20Meeting%20on%20a%20Foggy%20Train.mp3",
                "duration" => 66,
                "level" => LessonLevelEnum::Intermediate,
                "category_id" => 1,
                "source" => "The idiot",
                "is_free" => false
            ],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create($lesson);
        }
    }
}
