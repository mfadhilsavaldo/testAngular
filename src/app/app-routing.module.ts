import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { DashboardComponent } from './dashboard/dashboard.component';

const routes: Routes = [
  {
    
    path: '',
    component: DashboardComponent,
    children:[
      {
        path: 'master-data',
        loadChildren: () => import('./master-data/master-data.module').then(m=>m.MasterDataModule)
        
      }
    ]
  }
];


@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
