import { Component, OnInit, Inject } from '@angular/core';
import { MatTableDataSource } from '@angular/material/table';
import { HttpClient, HttpHeaders, HttpParams, HttpErrorResponse } from '@angular/common/http';
import { DataSource } from '@angular/cdk/table';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA, MatDialog } from '@angular/material/dialog';
import { environment } from 'src/environments/environment';
import Swal from 'sweetalert2';


@Component({
  selector: 'app-table',
  templateUrl: './table.component.html',
  styleUrls: ['./table.component.scss']
})
export class TableComponent implements OnInit {
  displayedColumns: string[] = ['no', 'nama', 'email', 'umur', 'action'];
  dataSource: any;

  constructor(
    private http: HttpClient,
    private dialog: MatDialog,
  ) {
    this.getData();
  }

  ngOnInit(): void {
  }

  getData() {
    let Header = new HttpHeaders();
    Header = Header.append('Authorization', '');

    let Data = new HttpParams();
    // Data = Data.append('year', this.year);

    this.http.get(environment.apiUrl + 'read', { params: Data }).subscribe(
      callback => {
        let response = <any>callback;
        if (response.success == true) {
          this.dataSource = new MatTableDataSource(response.data);
          console.log(this.dataSource)
        } else {
          alert(response.msg);
        }
      },//callback
      (err: HttpErrorResponse) => {
        console.log(err);
        console.log()
        if (err.error instanceof Error) {
          console.log('An error occurred:', err.error.message);
        } else {
          console.log('Backend returned code ' + err.status + ', body was : ' + err.error);
        }
      }//err
    );//subscribe
  }

  openAddDialog() {
    const dialogRef = this.dialog.open(TableAddComponent, {
      width: '400px',
      disableClose: true,
      data: { title: 'Create Data' }
    });

    dialogRef.afterClosed().subscribe(result => {
      console.log('The dialog was closed');
      if (result) {
        this.getData();
      }
    });
  }

  openEditDialog(data: any) {
    let dt = Object.assign({}, data);
    const dialogRef = this.dialog.open(TableEditComponent, {
      width: '400px',
      disableClose: true,
      data: dt
    });

    dialogRef.afterClosed().subscribe(result => {
      console.log('The dialog was closed');
      if (result) {
        this.getData();
      }
    });
  }

  onConfirmDialog(item: any) {
    Swal.fire({
      title: 'Apakah anda yakin?',
      text: "Anda tidak bisa mengembalikan data setelah dihapus!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, hapus!'
    }).then((result: any) => {
      if (result.value) {
        this.deleteData(item);
        Swal.fire(
          'Deleted!',
          'Your file has been deleted.',
          'success'
        )
      }
    })
  }

  deleteData(item: any) {
    const Header = { headers: new HttpHeaders().set('Authorization', '') };

    let Data = new HttpParams();

    Data = Data.append('iduser', item.iduser);

    this.http.post(environment.apiUrl + 'delete', Data).subscribe(
      callback => {
        let response = <any>callback;
        if (response.success == true) {
          this.getData();
        } else {
          alert(response.msg);
        }
      },
      (err: HttpErrorResponse) => {
        console.log(err);
      }//err
    );//http
  }//deleteData
}


// ------------------------------------------------ ADD DIALOG -----------------------------------
// -----------------------------------------------------------------------------------------------
@Component({
  selector: 'app-table-dialog',
  templateUrl: './table-add.component.html',
  styleUrls: ['./table.component.scss']
})

export class TableAddComponent implements OnInit {

  public groupList: any = [];

  disabled = false;
  myControl = new FormControl()
  dataform: any;
  private isUpdate = false;

  constructor(
    private formBuilder: FormBuilder,
    private http: HttpClient,
    @Inject(MAT_DIALOG_DATA) public data: any,
    public dialogRef: MatDialogRef<TableAddComponent>,

  ) {
    this.dataform = this.formBuilder.group({
      nama: ['', [Validators.required]],
      email: ['', [Validators.required]],
      umur: ['', [Validators.required]]
    })
  }

  ngOnInit(): void {
  }



  onCancel() {
    this.dialogRef.close(this.isUpdate);
  }

  onSave() {
    const Header = { headers: new HttpHeaders().set('Authorization', '') };

    let Data = new HttpParams();
    Data = Data.append('nama', this.dataform.get('nama').value);
    Data = Data.append('email', this.dataform.get('email').value);
    Data = Data.append('umur', this.dataform.get('umur').value);

    this.http.post(environment.apiUrl + 'create', Data).subscribe(
      callback => {

        console.log(callback);
        let response = <any>callback;
        if (response.success) {

          alert(response.msg);
          this.isUpdate = true;
          this.onCancel();
        } else {
          alert(response.msg);
        }
        console.log(callback)

      },
      (err: HttpErrorResponse) => {
        console.log(err)
      }//err
    );//http
  }

}

// ------------------------------------------------ EDIT DIALOG -----------------------------------
// -----------------------------------------------------------------------------------------------
@Component({
  selector: 'app-table-dialog',
  templateUrl: './table-edit.component.html',
  styleUrls: ['./table.component.scss']
})

export class TableEditComponent implements OnInit {

  public groupList: any = [];
  myControl = new FormControl()
  dataform: any;
  private isUpdate = false;

  constructor(
    private formBuilder: FormBuilder,
    private http: HttpClient,
    @Inject(MAT_DIALOG_DATA) public data: any,
    public dialogRef: MatDialogRef<TableEditComponent>,

  ) {
    console.log(data)
    this.dataform = this.formBuilder.group({
      iduser: [data.iduser],
      nama: [data.nama],
      email: [data.email],
      umur: [data.umur],
    })

  }

  ngOnInit(): void {
  }

  onCancel() {
    this.dialogRef.close(this.isUpdate);
  }
  onSave() {
    const Header = { headers: new HttpHeaders().set('Authorization', '') };

    let Data = new HttpParams();
    Data = Data.append('iduser', this.dataform.get('iduser').value);
    Data = Data.append('nama', this.dataform.get('nama').value);
    Data = Data.append('email', this.dataform.get('email').value);
    Data = Data.append('umur', this.dataform.get('umur').value);

    this.http.post(environment.apiUrl + 'edit', Data).subscribe(
      callback => {

        console.log(callback);
        let response = <any>callback;
        if (response.success) {
          alert(response.msg);
          this.isUpdate = true;
          this.onCancel();
        } else {
          alert(response.msg);
        }
        console.log(callback)

      },
      (err: HttpErrorResponse) => {
        console.log(err)
      }//err
    );//http
  }

}


