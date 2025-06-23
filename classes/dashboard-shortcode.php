<?php

class DashboardShortcode
{

    public $icon_order_history;
    public $icon_lab_history;
    public $icon_patient_profile;
    public $icon_returns;


    public function __construct()
    {
        add_shortcode('dashboard', [$this, 'render_dashboard']);
        $this->icons();
    }


    public function icons()
    {

        $this->icon_order_history = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" x="0" y="0" viewBox="0 0 512.089 512.089" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill="#ffe07d" d="M371.317 40.547c0-22.345 18.114-40.458 40.458-40.458H96.863c-17.769 0-32.174 14.405-32.174 32.174v366.381h306.627V40.547z" opacity="1" data-original="#ffe07d" class=""></path><path fill="#ffd064" d="M387.349.089c-22.345 0-40.458 18.114-40.458 40.458V200.99c0 9.44-6.2 17.838-15.286 20.4-66.999 18.892-116.107 80.457-116.107 153.5 0 19.203 3.396 37.613 9.617 54.663h146.202V40.547c0-22.345 18.114-40.458 40.458-40.458h-24.426z" opacity="1" data-original="#ffd064"></path><path fill="#2b4d66" d="M322.418 69.868H119.719c-4.268 0-7.728-3.459-7.728-7.728s3.459-7.728 7.728-7.728h202.699c4.268 0 7.728 3.459 7.728 7.728s-3.46 7.728-7.728 7.728zM322.418 146.926H119.719c-4.268 0-7.728-3.459-7.728-7.728s3.459-7.728 7.728-7.728h202.699c4.268 0 7.728 3.459 7.728 7.728s-3.46 7.728-7.728 7.728zM322.418 223.986H119.719a7.727 7.727 0 0 1-7.728-7.728 7.727 7.727 0 0 1 7.728-7.728h202.699c4.268 0 7.728 3.459 7.728 7.728s-3.46 7.728-7.728 7.728zM322.418 301.045H119.719c-4.268 0-7.728-3.459-7.728-7.728s3.459-7.728 7.728-7.728h202.699c4.268 0 7.728 3.459 7.728 7.728s-3.46 7.728-7.728 7.728z" opacity="1" data-original="#2b4d66" class=""></path><path fill="#eab14d" d="M334.352 429.555H57.201C25.61 429.555 0 403.945 0 372.353v-10.13a4.849 4.849 0 0 1 4.848-4.848h329.504z" opacity="1" data-original="#eab14d"></path><path fill="#e49542" d="M215.498 374.891c0 19.203 3.396 37.613 9.617 54.663h109.237v-72.18H216.46a161.127 161.127 0 0 0-.962 17.517z" opacity="1" data-original="#e49542"></path><circle cx="374.98" cy="374.891" r="137.109" fill="#dd636e" opacity="1" data-original="#dd636e"></circle><path fill="#da4a54" d="M374.98 237.782c-3.635 0-7.236.144-10.8.422 70.675 5.507 126.309 64.599 126.309 136.687s-55.635 131.18-126.309 136.687c3.564.278 7.165.422 10.8.422 75.723 0 137.109-61.386 137.109-137.109S450.703 237.782 374.98 237.782z" opacity="1" data-original="#da4a54"></path><circle cx="374.98" cy="374.891" r="102.268" fill="#f4fbff" opacity="1" data-original="#f4fbff"></circle><path fill="#daf1f4" d="M374.98 272.623c-4.739 0-9.4.33-13.967.953 49.873 6.812 88.301 49.572 88.301 101.315s-38.429 94.503-88.301 101.315c4.567.624 9.228.953 13.967.953 56.481 0 102.268-45.787 102.268-102.269 0-56.48-45.787-102.267-102.268-102.267z" opacity="1" data-original="#daf1f4"></path><path fill="#365e7d" d="M374.98 311.824a7.727 7.727 0 0 1-7.728-7.728v-3.54a7.727 7.727 0 0 1 7.728-7.728 7.727 7.727 0 0 1 7.728 7.728v3.54a7.727 7.727 0 0 1-7.728 7.728zM425.039 332.559a7.727 7.727 0 0 1-5.463-13.193l2.503-2.502a7.727 7.727 0 0 1 10.928.003 7.727 7.727 0 0 1-.003 10.928l-2.503 2.502a7.697 7.697 0 0 1-5.462 2.262zM449.314 382.618h-3.54c-4.268 0-7.728-3.459-7.728-7.728s3.459-7.728 7.728-7.728h3.54c4.268 0 7.728 3.459 7.728 7.728s-3.46 7.728-7.728 7.728zM304.185 382.618h-3.54c-4.268 0-7.728-3.459-7.728-7.728s3.459-7.728 7.728-7.728h3.54c4.268 0 7.728 3.459 7.728 7.728s-3.459 7.728-7.728 7.728zM427.543 435.181a7.704 7.704 0 0 1-5.463-2.262l-2.503-2.502a7.727 7.727 0 0 1-.003-10.928 7.723 7.723 0 0 1 10.928-.003l2.503 2.502a7.727 7.727 0 0 1-5.462 13.193zM374.98 456.953a7.727 7.727 0 0 1-7.728-7.728v-3.54a7.727 7.727 0 0 1 7.728-7.728 7.727 7.727 0 0 1 7.728 7.728v3.54a7.727 7.727 0 0 1-7.728 7.728zM322.418 435.181a7.728 7.728 0 0 1-5.465-13.192l2.502-2.502a7.728 7.728 0 1 1 10.929 10.929l-2.502 2.502a7.708 7.708 0 0 1-5.464 2.263zM324.92 332.559a7.712 7.712 0 0 1-5.465-2.263l-2.502-2.502a7.728 7.728 0 1 1 10.929-10.929l2.502 2.502a7.728 7.728 0 0 1-5.464 13.192z" opacity="1" data-original="#365e7d"></path><path fill="#2b4d66" d="M396.043 382.618H374.98a7.727 7.727 0 0 1-7.728-7.728v-40.786a7.727 7.727 0 0 1 7.728-7.728 7.727 7.727 0 0 1 7.728 7.728v33.058h13.335c4.268 0 7.728 3.459 7.728 7.728s-3.459 7.728-7.728 7.728z" opacity="1" data-original="#2b4d66" class=""></path><path fill="#eab14d" d="M445.031 101.613h-73.715V40.547c0-22.345 18.114-40.458 40.458-40.458 22.345 0 40.458 18.114 40.458 40.458v53.864a7.2 7.2 0 0 1-7.201 7.202z" opacity="1" data-original="#eab14d"></path><path fill="#e49542" d="M424.955 2.297a40.374 40.374 0 0 0-13.18-2.208c-22.344 0-40.458 18.114-40.458 40.458v61.066h26.36V40.547c-.001-17.728 11.407-32.782 27.278-38.25z" opacity="1" data-original="#e49542"></path></g></svg>';

        $this->icon_lab_history = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" x="0" y="0" viewBox="0 0 256 256" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill="#00779f" d="M175.021 58.467v163.117c0 9.112-7.387 16.5-16.5 16.5H35.82c-9.113 0-16.5-7.388-16.5-16.5V58.467c0-9.113 7.387-16.5 16.5-16.5H158.52c9.113 0 16.5 7.387 16.5 16.5z" opacity="1" data-original="#00779f" class=""></path><path fill="#d3ecfb" d="M154.647 56.2H39.693a5.5 5.5 0 0 0-5.5 5.5v156.65a5.5 5.5 0 0 0 5.5 5.5h114.954a5.5 5.5 0 0 0 5.5-5.5V61.7a5.5 5.5 0 0 0-5.5-5.5z" opacity="1" data-original="#d3ecfb" class=""></path><path fill="#bcbcbe" d="M140.458 41.442h-24.402v-.648c0-10.654-8.637-19.292-19.292-19.292-10.655 0-19.292 8.637-19.292 19.292v.648h-23.59a5.473 5.473 0 0 0-5.472 5.473v6.897c0 6.046 4.9 10.946 10.946 10.946h75.628c6.046 0 10.947-4.9 10.947-10.946v-6.897a5.473 5.473 0 0 0-5.473-5.473zm-34.942-.647a8.346 8.346 0 0 1-16.691 0 8.346 8.346 0 0 1 16.69 0z" opacity="1" data-original="#bcbcbe"></path><path fill="#d64349" d="M137.73 154.12H56.61a2.75 2.75 0 0 1-2.75-2.75V82.898h5.5v65.722h78.37v5.5z" opacity="1" data-original="#d64349"></path><path fill="#d64349" d="M56.61 136.246h8.662v5.5H56.61zM56.61 120.846h8.662v5.5H56.61zM56.61 105.447h8.662v5.5H56.61zM56.61 90.048h8.662v5.5H56.61z" opacity="1" data-original="#d64349"></path><path fill="#f59f13" d="M80.946 144.22a2.75 2.75 0 0 1-2.44-1.483l-9.36-18.032 4.882-2.534 6.531 12.584 11.315-29.736a2.75 2.75 0 0 1 4.157-1.268l12.692 8.964 12.848-22.912a2.75 2.75 0 0 1 2.4-1.405h11.81v5.5h-10.2l-13.553 24.169a2.75 2.75 0 0 1-3.985.9l-12.29-8.679-12.236 32.16a2.75 2.75 0 0 1-2.57 1.772z" opacity="1" data-original="#f59f13"></path><path fill="#00779f" d="M54.296 166.961h85.749v5.5H54.296zM54.296 181.134h85.749v5.5H54.296zM54.296 195.306h85.749v5.5H54.296z" opacity="1" data-original="#00779f" class=""></path><path fill="#bcbcbe" d="m198.565 193.328-18.29-34.836v-43.735h-28.43v43.735l-16.66 31.731c8.5 18.085 58.242 14.472 63.38 3.105z" opacity="1" data-original="#bcbcbe"></path><path fill="#d64349" d="m213.379 221.542-14.813-28.214c-6.364-.07-6.44-3.245-12.945-3.245-6.578 0-6.578 3.248-13.156 3.248-6.577 0-6.577-3.248-13.154-3.248-6.578 0-6.578 3.248-13.155 3.248-5.755 0-6.474-2.487-10.97-3.108l-16.445 31.319a5.047 5.047 0 0 0-.06 4.571l4.494 9.15a5.047 5.047 0 0 0 4.53 2.82h76.71a5.047 5.047 0 0 0 4.53-2.82l4.495-9.15a5.047 5.047 0 0 0-.061-4.571z" opacity="1" data-original="#d64349"></path><path fill="#bfcdd0" d="M131.468 223.509h69.185v5.5h-69.185z" opacity="1" data-original="#bfcdd0"></path><path fill="#d64349" d="M157.644 147.731h10.209v5.5h-10.209zM157.644 137.392h10.209v5.5h-10.209zM157.644 127.053h10.209v5.5h-10.209z" opacity="1" data-original="#d64349"></path><path fill="#bcbcbe" d="M151.933 121.022h28.254a5.853 5.853 0 0 0 0-11.705h-28.254a5.853 5.853 0 0 0 0 11.705zM228.257 145.773v-27.321h-24.21v28.916c3.553 7.584 21.06 13.142 24.21-1.595z" opacity="1" data-original="#bcbcbe"></path><path fill="#f59f13" d="M221.636 148.518c-5.437-.022-5.454-3.896-10.921-3.896-3.51 0-4.774 1.596-6.667 2.746v78.61c0 6.686 5.42 12.105 12.105 12.105 6.685 0 12.104-5.419 12.104-12.104v-80.206c-1.885 1.144-3.146 2.731-6.621 2.745z" opacity="1" data-original="#f59f13"></path><path fill="#bcbcbe" d="M203.6 121.022h25.105a5.853 5.853 0 0 0 0-11.705H203.6a5.853 5.853 0 0 0 0 11.705z" opacity="1" data-original="#bcbcbe"></path></g></svg>';

        $this->icon_patient_profile = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g fill="#fed2a4"><path d="m452.109 120.128-1 2.41v288.858l1 2.476C482.809 372.946 501 322.098 501 267s-18.191-105.946-48.891-146.872zM60.891 123.058l-1-2.931C29.191 161.054 11 211.901 11 267s18.191 105.946 48.891 146.872l1-2.931z" fill="#fed2a4" opacity="1" data-original="#fed2a4" class=""></path></g><path fill="#0593fc" d="M438.918 0H73.082c-7.285 0-13.191 5.906-13.191 13.191v36.056l3.268.879H448.84l3.268-.879V13.191C452.109 5.906 446.203 0 438.918 0z" opacity="1" data-original="#0593fc"></path><path fill="#0182fc" d="M101.783 13.191C101.783 5.906 107.688 0 114.974 0H73.082c-7.285 0-13.191 5.906-13.191 13.191v36.056l3.268.879h41.891l-3.268-.879V13.191z" opacity="1" data-original="#0182fc"></path><path fill="#eaf6ff" d="M59.891 413.872C104.584 473.451 175.789 512 256 512s151.417-38.549 196.109-98.128V49.247H59.891z" opacity="1" data-original="#eaf6ff"></path><path fill="#d8ecfe" d="M101.783 49.247H59.891v364.625a246.422 246.422 0 0 0 41.891 43.479V49.247z" opacity="1" data-original="#d8ecfe" class=""></path><circle cx="87.085" cy="25.558" r="7.5" fill="#22b27f" opacity="1" data-original="#22b27f"></circle><circle cx="111.085" cy="25.558" r="7.5" fill="#fe9901" opacity="1" data-original="#fe9901"></circle><circle cx="135.085" cy="25.558" r="7.5" fill="#fe646f" opacity="1" data-original="#fe646f"></circle><path fill="#60b7ff" d="M423.993 33.058h-27a7.5 7.5 0 0 1 0-15h27a7.5 7.5 0 0 1 0 15z" opacity="1" data-original="#60b7ff"></path><path fill="#22b27f" d="M319.883 469.431H192.117c-5.523 0-10-4.477-10-10V432c0-5.523 4.477-10 10-10h127.766c5.523 0 10 4.477 10 10v27.431c0 5.523-4.477 10-10 10z" opacity="1" data-original="#22b27f"></path><path fill="#09a76d" d="M224 459.431V432c0-5.523 4.477-10 10-10h-41.883c-5.523 0-10 4.477-10 10v27.431c0 5.523 4.477 10 10 10H234c-5.523 0-10-4.477-10-10z" opacity="1" data-original="#09a76d"></path><path fill="#57be92" d="M295.678 453.215h-79.355a7.5 7.5 0 0 1 0-15h79.355a7.5 7.5 0 0 1 0 15z" opacity="1" data-original="#57be92"></path><path fill="#8ac9fe" d="M406.419 202.697H308.34a7.5 7.5 0 0 1 0-15h98.079a7.5 7.5 0 0 1 0 15zM406.419 234.858H308.34a7.5 7.5 0 0 1 0-15h98.079a7.5 7.5 0 0 1 0 15zM406.419 267.021H308.34a7.5 7.5 0 0 1 0-15h98.079a7.5 7.5 0 0 1 0 15z" opacity="1" data-original="#8ac9fe"></path><path fill="#b3dafe" d="M281.341 202.697h-55.979a7.5 7.5 0 0 1 0-15h55.979a7.5 7.5 0 0 1 0 15zM281.341 234.858h-55.979a7.5 7.5 0 0 1 0-15h55.979a7.5 7.5 0 0 1 0 15zM281.341 267.021h-55.979a7.5 7.5 0 0 1 0-15h55.979a7.5 7.5 0 0 1 0 15z" opacity="1" data-original="#b3dafe"></path><path fill="#60b7ff" d="M406.419 305.5H225.362a7.5 7.5 0 0 1 0-15h181.057a7.5 7.5 0 0 1 0 15zM406.419 337.662H225.362a7.5 7.5 0 0 1 0-15h181.057a7.5 7.5 0 0 1 0 15z" opacity="1" data-original="#60b7ff"></path><path fill="#26a6fe" d="m190.078 218.194-22.57-13.031a33.367 33.367 0 0 1-20.203 6.787 33.374 33.374 0 0 1-20.204-6.787l-22.57 13.031a9.638 9.638 0 0 0-4.819 8.346v30.842a9.638 9.638 0 0 0 9.638 9.638h75.909a9.638 9.638 0 0 0 9.638-9.638V226.54a9.642 9.642 0 0 0-4.819-8.346z" opacity="1" data-original="#26a6fe"></path><path fill="#0593fc" d="M132.55 257.383v-30.842a9.636 9.636 0 0 1 4.819-8.346l10.854-6.267c-.306.008-.611.023-.919.023a33.374 33.374 0 0 1-20.204-6.787l-22.57 13.031a9.638 9.638 0 0 0-4.819 8.346v30.842a9.638 9.638 0 0 0 9.638 9.638h32.838c-5.322 0-9.637-4.315-9.637-9.638z" opacity="1" data-original="#0593fc"></path><circle cx="147.304" cy="178.405" r="34.602" fill="#0593fc" opacity="1" data-original="#0593fc"></circle><path fill="#0182fc" d="M140.478 178.405c0-14.169 8.521-26.342 20.714-31.693a34.47 34.47 0 0 0-13.888-2.909c-19.11 0-34.602 15.492-34.602 34.602s15.492 34.602 34.602 34.602a34.47 34.47 0 0 0 13.888-2.909c-12.193-5.351-20.714-17.524-20.714-31.693z" opacity="1" data-original="#0182fc"></path><path fill="#22b27f" d="M124.07 127.933h-20.989a5 5 0 0 1-5-5v-20.989a5 5 0 0 1 5-5h20.989a5 5 0 0 1 5 5v20.989a5 5 0 0 1-5 5z" opacity="1" data-original="#22b27f"></path><path fill="#09a76d" d="M111 122.933v-20.989a5 5 0 0 1 5-5h-12.919a5 5 0 0 0-5 5v20.989a5 5 0 0 0 5 5H116a5 5 0 0 1-5-5z" opacity="1" data-original="#09a76d"></path><path fill="#22b27f" d="M163.33 127.933h-20.989a5 5 0 0 1-5-5v-20.989a5 5 0 0 1 5-5h20.989a5 5 0 0 1 5 5v20.989a5 5 0 0 1-5 5z" opacity="1" data-original="#22b27f"></path><path fill="#09a76d" d="M150.26 122.933v-20.989a5 5 0 0 1 5-5h-12.919a5 5 0 0 0-5 5v20.989a5 5 0 0 0 5 5h12.919a5 5 0 0 1-5-5z" opacity="1" data-original="#09a76d"></path><path fill="#8ac9fe" d="M202.59 127.933h-20.989a5 5 0 0 1-5-5v-20.989a5 5 0 0 1 5-5h20.989a5 5 0 0 1 5 5v20.989a5 5 0 0 1-5 5z" opacity="1" data-original="#8ac9fe"></path><path fill="#60b7ff" d="M189.52 122.933v-20.989a5 5 0 0 1 5-5h-12.919a5 5 0 0 0-5 5v20.989a5 5 0 0 0 5 5h12.919a5 5 0 0 1-5-5z" opacity="1" data-original="#60b7ff"></path><path fill="#22b27f" d="M410.522 97.75h-19.319a3.397 3.397 0 0 1-3.397-3.397V75.034a3.397 3.397 0 0 0-3.397-3.397h-22.583a3.397 3.397 0 0 0-3.397 3.397v19.319a3.397 3.397 0 0 1-3.397 3.397h-19.319a3.397 3.397 0 0 0-3.397 3.397v22.583a3.397 3.397 0 0 0 3.397 3.397h19.319a3.397 3.397 0 0 1 3.397 3.397v19.319a3.397 3.397 0 0 0 3.397 3.397h22.583a3.397 3.397 0 0 0 3.397-3.397v-19.319a3.397 3.397 0 0 1 3.397-3.397h19.319a3.397 3.397 0 0 0 3.397-3.397v-22.583a3.397 3.397 0 0 0-3.397-3.397z" opacity="1" data-original="#22b27f"></path><path fill="#09a76d" d="M377.511 137.604a3.397 3.397 0 0 1-3.397-3.397v-19.319a3.397 3.397 0 0 0-3.397-3.397h-19.319a3.397 3.397 0 0 1-3.397-3.397V97.75h-12.288a3.397 3.397 0 0 0-3.397 3.397v22.583a3.397 3.397 0 0 0 3.397 3.397h19.319a3.397 3.397 0 0 1 3.397 3.397v19.319a3.397 3.397 0 0 0 3.397 3.397h22.583a3.397 3.397 0 0 0 3.397-3.397v-12.239z" opacity="1" data-original="#09a76d"></path><circle cx="105.581" cy="300.548" r="7.5" fill="#fe646f" opacity="1" data-original="#fe646f"></circle><circle cx="132.309" cy="300.548" r="7.5" fill="#b3dafe" opacity="1" data-original="#b3dafe"></circle><circle cx="159.037" cy="300.548" r="7.5" fill="#b3dafe" opacity="1" data-original="#b3dafe"></circle><circle cx="185.765" cy="300.548" r="7.5" fill="#fe646f" opacity="1" data-original="#fe646f"></circle><circle cx="105.581" cy="327" r="7.5" fill="#8ac9fe" opacity="1" data-original="#8ac9fe"></circle><circle cx="132.309" cy="327" r="7.5" fill="#60b7ff" opacity="1" data-original="#60b7ff"></circle><circle cx="159.037" cy="327" r="7.5" fill="#fe646f" opacity="1" data-original="#fe646f"></circle><circle cx="185.765" cy="327" r="7.5" fill="#8ac9fe" opacity="1" data-original="#8ac9fe"></circle><path fill="#b3dafe" d="M407.039 387.5H104.961a7.5 7.5 0 0 1 0-15H407.04a7.5 7.5 0 1 1-.001 15z" opacity="1" data-original="#b3dafe"></path></g></svg>';

        $this->icon_returns = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g fill-rule="evenodd" clip-rule="evenodd"><path fill="#f2d1a5" d="m227.6 19.35 190.291 109.864c6.213 3.587 9.77 9.747 9.77 16.921v219.729c0 7.175-3.556 13.334-9.77 16.921L227.6 492.65c-6.213 3.587-13.326 3.587-19.539 0L17.771 382.786c-6.214-3.587-9.77-9.747-9.77-16.921V146.136c0-7.175 3.556-13.335 9.77-16.921L208.062 19.35c6.213-3.587 13.325-3.587 19.538 0z" opacity="1" data-original="#f2d1a5" class=""></path><path fill="#e3a76f" d="M425.106 136.329c1.66 2.875 2.549 6.196 2.555 9.769v219.804c-.012 7.159-3.567 13.303-9.77 16.884L227.6 492.65c-3.107 1.794-6.438 2.69-9.769 2.69V256z" opacity="1" data-original="#e3a76f" class=""></path><path fill="#c48958" d="m227.6 19.35 190.291 109.865c3.106 1.793 5.549 4.23 7.215 7.115L217.831 256 10.556 136.329c1.666-2.885 4.108-5.321 7.215-7.115L208.062 19.35c6.213-3.587 13.325-3.587 19.538 0z" opacity="1" data-original="#c48958" class=""></path><path fill="#d1d1d6" d="m82.491 177.861 60.851 35.132L353.171 91.848 292.32 56.716z" opacity="1" data-original="#d1d1d6" class=""></path><path fill="#eceff1" d="m136.014 304.172-48.638-28.081c-3.107-1.794-4.885-4.873-4.885-8.461v-89.769l60.85 35.132v86.949c0 1.793-.889 3.333-2.442 4.23s-3.331.897-4.885 0z" opacity="1" data-original="#eceff1"></path><path fill="#6cf5c2" d="M356.928 340.093v25.239c0 1.931-1.017 3.587-2.74 4.46s-3.659.714-5.217-.429l-74.248-54.473c-2.561-1.879-3.978-4.568-4.08-7.743-.101-3.174 1.141-5.949 3.577-7.988l74.499-62.353c1.524-1.276 3.524-1.537 5.325-.696s2.884 2.542 2.884 4.53v23.046c181.606 0 191.574 187.85 48.978 222.924a.994.994 0 0 1-1.159-.57.995.995 0 0 1 .456-1.291c85.826-44.856 56.215-144.656-48.275-144.656z" opacity="1" data-original="#6cf5c2" class=""></path><path fill="#00e499" d="M294.824 281.915c165.095-18.856 234.849 66.213 166.16 178.771 80.346-62.688 49.504-196.998-104.056-196.998v-23.046c0-1.988-1.083-3.689-2.884-4.53s-3.8-.58-5.325.696z" opacity="1" data-original="#00e499" class=""></path></g></g></svg>';
    }

    public function render_dashboard()
    {
        ob_start();
?>

        <section id="hdlDashboard">

            <div class="container">
                <div class="tabs">
                    <input type="radio" id="tab1" name="tab-control" checked />
                    <input type="radio" id="tab2" name="tab-control" />
                    <input type="radio" id="tab3" name="tab-control" />
                    <input type="radio" id="tab4" name="tab-control" />

                    <ul>
                        <li title="Features">
                            <label for="tab1" role="button">
                                <span><?php echo $this->icon_order_history ?></span>
                                <span>Order History</span>
                            </label>
                        </li>
                        <li title="Delivery Contents">
                            <label for="tab2" role="button">
                                <span><?php echo $this->icon_lab_history ?></span>
                                <span>Lab Orders</span>
                            </label>
                        </li>
                        <li title="Shipping">
                            <label for="tab3" role="button">
                                <span><?php echo $this->icon_patient_profile ?></span>
                                <span>Patient Profile</span>
                            </label>
                        </li>
                        <li title="Returns">
                            <label for="tab4" role="button">
                                <span><?php echo $this->icon_returns ?></span>
                                <span>Subscriptions</span>
                            </label>
                        </li>
                    </ul>

                    <div class="slider">
                        <div class="indicator"></div>
                    </div>

                    <div class="content">
                        <section>
                            <h2>Order History</h2>
                            <div class="inner-content">
                                <?php include plugin_dir_path(__FILE__) . 'tabs/show-orders.php'; ?>
                            </div>
                        </section>
                        <section>
                            <h2>Lab Orders</h2>
                            <?php include plugin_dir_path(__FILE__) . 'tabs/lab-orders.php'; ?>
                        </section>
                        <section>
                            <h2>Patient Profile</h2>
                            <?php include plugin_dir_path(__FILE__) . 'tabs/patient-profile.php'; ?>
                        </section>
                        <section>
                            <h2>Subscriptions</h2>
                            <?php include plugin_dir_path(__FILE__) . 'tabs/returns.php'; ?>
                        </section>
                    </div>
                </div>

            </div>
        </section>

<?php
        return ob_get_clean();
    }
}
