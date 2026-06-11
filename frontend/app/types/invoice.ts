export interface Invoice {
    id: number;
    number: string;
    supplier_name: string;
    supplier_tax_id: string;
    net_amount: number;
    vat_amount: number;
    gross_amount: number;
    currency: string;
    status: string;
    issue_date: string;
    due_date: string;
}
