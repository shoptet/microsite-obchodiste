<section class="section section-primary">
    <div class="section-inner container">
        Primary section
    </div>
</section>

<section class="section section-secondary">
    <div class="section-inner container">
        Secondary section
    </div>
</section>

<section class="section section-perex">
    <div class="section-inner container">
        Tertiary section
    </div>
</section>

<section class="section section-primary">
    <div class="section-inner container">
        <h1>Heading level 1</h1>
        <h2>Heading level 2</h2>
        <ul>
            <li>Unordered list item with <a href="#">link</a></li>
            <li>Unordered list item</li>
            <li>Another unordered list item</li>
        </ul>
        <h3>Heading level 3</h3>
        <ol>
            <li>Ordered list item with <a href="#">link</a></li>
            <li>Ordered list item</li>
            <li>Another ordered list item</li>
        </ol>
        <h4>Heading level 4</h4>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin iaculis vestibulum magna nec auctor. Donec non nulla id lectus semper dictum vel sit amet nunc. Nulla a velit odio. Morbi convallis tempus arcu, in eleifend erat porta ac. Mauris maximus ornare orci, in aliquam diam viverra et. Praesent pretium, neque quis tristique tincidunt, metus arcu laoreet enim, vel lobortis lectus nibh vel orci. Mauris a aliquam ligula. Praesent id lectus et quam faucibus blandit eget vitae libero. In id imperdiet purus, eget hendrerit lacus. Ut cursus quis velit quis maximus.
        </p>
        <h5>Heading level 5</h5>
        <p>
            Etiam a risus vel lorem eleifend hendrerit in non elit. Cras accumsan enim rhoncus laoreet facilisis. Maecenas faucibus eu ipsum ornare tincidunt. Quisque feugiat, justo at tristique dapibus, erat mi elementum ex, sed tincidunt libero justo ac massa. Aliquam vehicula rutrum ipsum at ornare. Aenean mollis, magna pharetra placerat tincidunt, nunc justo varius magna, et finibus erat orci et sapien. Maecenas dictum lacus in leo elementum, id auctor lectus ornare. Pellentesque odio elit, tempor ut auctor eget, aliquam scelerisque diam. Mauris quam augue, tempus sit amet urna non, vestibulum tincidunt ex. Etiam et viverra erat.
        </p>
        <h6>Heading level 6</h6>
        <p>
            Integer rhoncus nunc diam, at luctus purus tincidunt sit amet. Nulla a tortor nunc. In ullamcorper feugiat iaculis. Nullam placerat elit nisi, pellentesque vestibulum eros porttitor ut. Integer quis rhoncus nulla. Donec vel ex eros. Aliquam erat volutpat. Integer rutrum lacus eget mauris cursus, nec sodales sapien eleifend. Nunc sodales arcu elit, vitae laoreet elit finibus ac. Aliquam hendrerit, sem ut malesuada cursus, risus mi convallis velit, sit amet luctus risus tellus et dolor.
        </p>
        <ul class="pagination">
            <li class="page-item disabled">
                <span class="page-link">1</span>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">3</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Next</a>
            </li>
        </ul>
    </div>
</section>

<section class="section section-primary">
    <div class="section-inner container">
        <a href="#shp_demo_colorboxDemo" class="colorbox">Colorbox</a>
        <br />
        <style>
            #shp_demo_colorboxDemo { display: none; }
            #colorbox #shp_demo_colorboxDemo { display: block; }
        </style>
        <span id="shp_demo_colorboxDemo">
                Lorem ipsum.
            </span>
        <a href="#" id="shp_demo_showOverlay">Show overlay</a>
    </div>
</section>

<section class="section section-primary">
    <div class="section-inner container">
        <div class="halfs aligned-left">
            <div class="half">
                <h2>Contact form</h2>
                <p>
                    Lorem ipsum dolor sit amet.
                </p>
            </div>
            <div class="half">
                <?php get_template_part( 'template-parts/utils/content', 'contact-form' ); ?>
            </div>
        </div>
    </div>
</section>

<section class="section section-primary">
    <div class="section-inner container">
        <div class="alert alert-warning" role="alert">
            <div class="row">
                <div class="col-sm-2 col-lg-1 text-center">
                    <i class="alert-icon fas fa-exclamation-circle"></i>
                </div>
                <div class="col-sm-10 col-lg-11">
                    <h4 class="alert-heading">Ea possunt paria non esse</h4>
                    Atqui iste <a href="https://skolka.shoptet.cz/kam-dal/">locus </a>est, Piso, tibi etiam atque etiam confirmandus, inquam
                </div>
            </div>
        </div>
        <div class="alert alert-danger" role="alert">
            <div class="row">
                <div class="col-sm-2 col-lg-1 text-center">
                    <i class="alert-icon fas fa-times-circle"></i>
                </div>
                <div class="col-sm-10 col-lg-11">
                    Atqui iste locus est, Piso, tibi etiam atque etiam confirmandus, inquam
                </div>
            </div>
        </div>
        <div class="alert alert-success" role="alert">
            <div class="row">
                <div class="col-sm-2 col-lg-1 text-center">
                    <i class="alert-icon fas fa-check-circle"></i>
                </div>
                <div class="col-sm-10 col-lg-11">
                    <p>
                        Atqui iste locus est, Piso, tibi etiam  atque etiam confirmandus, inquam
                    </p>
                    <ul>
                        <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                        <li>Quisque aliquam tempus ultricies</li>
                        <li>Morbi condimentum mollis lacinia</li>
                        <li>Curabitur venenatis</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="alert alert-info" role="alert">
            <div class="row">
                <div class="col-sm-2 col-lg-1 text-center">
                    <i class="alert-icon fas fa-lightbulb"></i>
                </div>
                <div class="col-sm-10 col-lg-11">
                    <p>
                        Atqui iste locus est, Piso, tibi etiam  atque etiam confirmandus, inquam
                    </p>
                    <ul>
                        <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit</li>
                        <li>Quisque aliquam tempus ultricies</li>
                        <li>Morbi condimentum mollis lacinia</li>
                        <li>Curabitur venenatis</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Ea possunt paria non esse</h4>
            Atqui iste locus est, Piso, tibi etiam atque etiam confirmandus, inquam
        </div>
        <div class="alert alert-danger" role="alert">
            Atqui iste locus est, Piso, tibi etiam atque etiam confirmandus, inquam
        </div>
        <div class="alert alert-success" role="alert">
            Atqui iste locus est, Piso, tibi etiam atque etiam confirmandus, inquam
        </div>
        <div class="alert alert-info alert-dismissible" role="alert">
            <h4 class="alert-heading">Ea possunt paria non esse</h4>
            Atqui iste locus est, Piso, tibi etiam atque etiam confirmandus, inquam
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    </div>
</section>
