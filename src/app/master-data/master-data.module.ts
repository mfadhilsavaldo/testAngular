import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TableComponent } from './table/table.component';
import { MasterTableComponent } from './master-table/master-table.component';
import { RouterModule, Routes } from '@angular/router';

const routes: Routes = [
  {
    path: 'table',
    component: TableComponent
  },
  {
    path: 'master-table',
    component: MasterTableComponent
  }
];

@NgModule({
  declarations: [
    TableComponent,
    MasterTableComponent
  ],
  imports: [
    CommonModule,
    RouterModule.forChild(routes),
  ],

})
export class MasterDataModule { }
