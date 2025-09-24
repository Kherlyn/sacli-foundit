@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    Previous
                </span>
-gray- bordererg-white bord0 bt-gray-70m texediunt-mfo text-sm ml-px px-4 py-2 -ems-centerne-flex itlative inliass="reurl }}" clhref="{{ $       <a                             @else
                                 
n> </spa                                  >
 e }}</span{ $pag">{leading-5r-default 400 cursosacli-green-border-border en-400 g-sacli-gret-white bm texdiu-sm font-me -ml-px textpx-4 py-2tems-center x i-fleinenltive ielan class="r     <spa                                  
 ">"pagea-current=<span ari                             
       ge())ntPanator->curre == $pagi@if ($page                               )
 urle => $as $pagent reach ($elem @fo                         nt))
  lemeray($ef (is_ar      @i          }
        s --}Link Of  Array       {{--               
  
dif      @en                
  span>     </              >
         pannt }}</s">{{ $elemeing-5lt lead-defauy-300 cursorder-graborr ordee b0 bg-whit-gray-70um textm font-mediml-px text-spy-2 -4 er px-ms-centlex itee-finlinelative ="rclass    <span                        
     "true">a-disabled=  <span ari                         
 ($element))_stringf (is @i                       ator --}}
s" Separe Dotre {{-- "Th                   ent)
     as $elemementsreach ($el    @fo               
 ments --}}n Ele- Paginatio{{-                    if

end         @           
 </a>                  >
       </svg                       />
    dd"le="evenolip-ruz" c0 011.414 0414l4-4a1 1 1 0 010-1.-4a1  1.414l-4.41401-1293a1 1 0 93 3.4 10l3.214L9.410 010 1.493a1 1 2.707 5.2d="M1d" enode="evrul <path fill-                              20">
 "0 0 20 x=or" viewBoolcurrentCill=" f5" h- class="w-5svg          <           >
       "Previous"ria-label=n-150" a-out duratioon ease-in0 transiti-gray-50tive:text acg-gray-100300 active:b-green-cli:border-saen-300 focusli-greacng-socus:ring ritline-none fs:oufocuz-10 focus:gray-400  hover:text- leading-5nded-l-mdrou300 ray-der border-gite bory-500 bg-whtext-graium t-medsm font-2 py-2 tex-center px-temslex ive inline-fatilass="rel" c"prev}" rel=sPageUrl() }oureviator->ppagin"{{ $ href=         <a            else
       @              /span>
       <                 n>
  /spa   <                        /svg>
     <                            
evenodd" />e="p-rul" cli 0z4141 0 011..414l4-4a1  010-1l-4-4a1 1 0414.414 1.a1 1 0 01-1 3.2933.2930l14L9.414 1 1.40100 93a1 1 12.707 5.2odd" d="M"evenrule=fill-   <path                                20">
  20 ="0 0 Boxlor" view="currentCofill"w-5 h-5" vg class=     <s                       e">
    ="truria-hidden a5"md leading-t rounded-l-faulr-de-300 cursor-grayordeborder b-white 500 bgm text-gray-diu-sm font-me py-2 text-2nter px-ceex itemse inline-fl"relativan class=        <sp            
        "Previous">ia-label=e" arbled="tru-disa<span aria                      ())
  onFirstPageginator->($pa      @if            --}}
    ge Linkious Pa{-- Prev  {                
  ded-md">oundow-sm r-flex sha inlineive z-0latss="recla      <span 
            <div>          >

</div             </p>
          
         results              }</span>
  otal() }paginator->tm">{{ $iu"font-medn class=spa       <                of
            
         @endif        }}
        t() ->coun{ $paginator   {                     
se    @el              an>
  m() }}</sptor->lastItegina{ $paedium">{ont-m"fan class=   <sp                         to
               n>
     }</spaItem() }nator->first">{{ $pagiont-mediumss="fclaspan          <          ())
     Itemirstpaginator->f@if ($               owing
       Sh                ng-5">
  dilea700 -gray-text="text-sm p class   <          
        <div>">
       betweenfy-usticenter sm:jx sm:items- sm:fle:flex-1sm"hidden iv class=        <d  </div>

ndif
         @e        n>
        </spa
                 Next            ded-md">
oun-5 rt leadingefaulcursor-dray-300 er border-gte bordy-500 bg-whim text-graiuont-med-3 text-sm f2 mler px-4 py-ent items-cine-flexelative inls="rn clas       <spa      @else
    
              </a>          Next
                       n-150">
atiodure-in-out tion eas00 transi-gray-7ext active:tg-gray-100:bctive300 aacli-green-rder-sbos:en-300 focucli-gre-sang ringfocus:riine-none utl00 focus:oext-gray-5 hover:tmdd-ndeeading-5 rou-gray-300 l borderhite border bg-w700ay-grmedium text-ext-sm font-py-2 ml-3 tpx-4 er tems-centnline-flex iative iel"r}}" class=xtPageUrl() aginator->ne"{{ $p  <a href=      )
        orePages()>hasMnator-gi @if ($pa           endif

         @/a>
         <
          ious   Prev              0">
   15on-urati-in-out dn ease transitiot-gray-700tive:tex-gray-100 ac:bg300 activereen-i-gcl-sardercus:boeen-300 focli-grg-sarin focus:ring noneine-us:outlray-500 foct-gr:texed-md hoveng-5 roundadiy-300 ler border-graite bordewh0 bg--gray-70edium textt-sm font-mtex-4 py-2 -center pxemslex ite inline-fivelatlass="rUrl() }}" cousPagetor->previinapag{ $ref="{      <a h              @else
        