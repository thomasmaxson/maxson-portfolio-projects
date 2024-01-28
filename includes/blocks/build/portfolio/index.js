(()=>{"use strict";var e,r={837:(e,r,n)=>{const o=window.wp.blocks,t=JSON.parse('{"u2":"maxson-portfolio-projects/portfolio"}'),l=window.React,a=window.wp.i18n,i=window.wp.serverSideRender;var s=n.n(i);const u=window.wp.blockEditor,c=window.wp.components;(0,o.registerBlockType)(t.u2,{edit:function({attributes:e,setAttributes:r}){const n=(0,u.useBlockProps)({className:"portfolio-project-archive-block"}),{columns:o,requireThumb:i,numberOfItems:m,order:p,orderBy:d}=e;return(0,l.createElement)(l.Fragment,null,(0,l.createElement)(u.InspectorControls,null,(0,l.createElement)(c.PanelBody,{title:(0,a.__)("Layout","maxson"),initialOpen:!0},(0,l.createElement)(c.PanelRow,null,(0,l.createElement)(c.RangeControl,{label:(0,a.__)("Desktop Column Count","maxson"),help:(0,a.__)("Maximum number of columns to display on large screens","maxson"),value:o,min:1,max:6,step:1,onChange:e=>r({columns:Number.isNaN(e)?1:e})}))),(0,l.createElement)(c.PanelBody,{title:(0,a.__)("Settings","maxson"),initialOpen:!1},(0,l.createElement)(c.PanelRow,null,(0,l.createElement)(c.ToggleControl,{label:(0,a.__)("Require thumbnail to show project","maxson"),checked:i,onChange:e=>r({requireThumb:e})})),(0,l.createElement)(c.QueryControls,{numberOfItems:m,order:p,orderBy:d,onOrderChange:e=>r({order:e}),onOrderByChange:e=>r({orderBy:e}),onNumberOfItemsChange:e=>r({numberOfItems:e})}))),(0,l.createElement)("div",{...n},(0,l.createElement)(c.Disabled,null,(0,l.createElement)(s(),{block:t.u2,skipBlockSupportAttributes:!0,attributes:e}))))},save:function(){return null}})}},n={};function o(e){var t=n[e];if(void 0!==t)return t.exports;var l=n[e]={exports:{}};return r[e](l,l.exports,o),l.exports}o.m=r,e=[],o.O=(r,n,t,l)=>{if(!n){var a=1/0;for(c=0;c<e.length;c++){for(var[n,t,l]=e[c],i=!0,s=0;s<n.length;s++)(!1&l||a>=l)&&Object.keys(o.O).every((e=>o.O[e](n[s])))?n.splice(s--,1):(i=!1,l<a&&(a=l));if(i){e.splice(c--,1);var u=t();void 0!==u&&(r=u)}}return r}l=l||0;for(var c=e.length;c>0&&e[c-1][2]>l;c--)e[c]=e[c-1];e[c]=[n,t,l]},o.n=e=>{var r=e&&e.__esModule?()=>e.default:()=>e;return o.d(r,{a:r}),r},o.d=(e,r)=>{for(var n in r)o.o(r,n)&&!o.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:r[n]})},o.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r),(()=>{var e={119:0,805:0};o.O.j=r=>0===e[r];var r=(r,n)=>{var t,l,[a,i,s]=n,u=0;if(a.some((r=>0!==e[r]))){for(t in i)o.o(i,t)&&(o.m[t]=i[t]);if(s)var c=s(o)}for(r&&r(n);u<a.length;u++)l=a[u],o.o(e,l)&&e[l]&&e[l][0](),e[l]=0;return o.O(c)},n=globalThis.webpackChunkarchive=globalThis.webpackChunkarchive||[];n.forEach(r.bind(null,0)),n.push=r.bind(null,n.push.bind(n))})();var t=o.O(void 0,[805],(()=>o(837)));t=o.O(t)})();