<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    ['label' => 'Яблоки', 'icon' => 'fa fa-apple', 'url' => ['apple/index'],],
                    ['label' => 'Пользователи', 'icon' => 'fa fa-users', 'url' => ['user/index'],],
                ],
            ]
        ) ?>

    </section>

</aside>
